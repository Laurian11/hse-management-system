<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * ZKTeco TCP Protocol Handler
 * 
 * Handles binary protocol communication with ZKTeco devices via TCP socket
 * Port 4370 uses proprietary binary protocol, not HTTP
 */
class ZKTecoTCPProtocol
{
    private $socket;
    private $ip;
    private $port;
    private $connected = false;

    // ZKTeco Protocol Constants
    const CMD_CONNECT = 1000;
    const CMD_EXIT = 1001;
    const CMD_ACK_OK = 2000;
    const CMD_ACK_ERROR = 2001;
    const CMD_ACK_DATA = 2002;
    const CMD_ACK_RETRY = 2003;
    const CMD_ACK_REPEAT = 2004;
    const CMD_ACK_UNAUTH = 2005;
    const CMD_PREPARE_DATA = 1500;
    const CMD_DATA = 1501;
    const CMD_DATA_WRRQ = 1502;
    const CMD_DATA_ACK = 1503;
    const CMD_DATA_EOF = 1504;
    const CMD_GET_USER = 8;
    const CMD_SET_USER = 5;
    const CMD_DELETE_USER = 6;
    const CMD_GET_ATTENDANCE = 13;
    const CMD_CLEAR_ATTENDANCE = 14;
    const CMD_SET_TIME = 201;
    const CMD_GET_TIME = 202;
    const CMD_VERSION = 1100;
    const CMD_DEVICE = 11;

    public function __construct(string $ip, int $port = 4370)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * Connect to device
     */
    public function connect(): bool
    {
        try {
            $this->socket = @fsockopen($this->ip, $this->port, $errno, $errstr, 5);
            
            if (!$this->socket) {
                Log::error("ZKTeco TCP connection failed: {$errstr} ({$errno})");
                return false;
            }

            // Set socket timeout
            stream_set_timeout($this->socket, 5);

            // Send connect command
            $command = $this->createCommand(self::CMD_CONNECT, 0, 0);
            $this->send($command);

            // Read response
            $response = $this->receive();
            
            if ($response && $this->getCommand($response) == self::CMD_ACK_OK) {
                $this->connected = true;
                return true;
            }

            $this->disconnect();
            return false;
        } catch (\Exception $e) {
            Log::error("ZKTeco TCP connect error: " . $e->getMessage());
            $this->disconnect();
            return false;
        }
    }

    /**
     * Disconnect from device
     */
    public function disconnect(): void
    {
        if ($this->socket && $this->connected) {
            try {
                $command = $this->createCommand(self::CMD_EXIT, 0, 0);
                $this->send($command);
            } catch (\Exception $e) {
                // Ignore errors on disconnect
            }
        }

        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
        }

        $this->connected = false;
    }

    /**
     * Get device version/info
     */
    public function getVersion(): ?array
    {
        if (!$this->connected) {
            return null;
        }

        try {
            $command = $this->createCommand(self::CMD_VERSION, 0, 0);
            $this->send($command);
            
            $response = $this->receive();
            
            if ($response && $this->getCommand($response) == self::CMD_ACK_OK) {
                $data = $this->getData($response);
                return [
                    'version' => trim($data),
                    'protocol' => 'tcp',
                    'port' => $this->port
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error("ZKTeco getVersion error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get device time
     */
    public function getTime(): ?\DateTime
    {
        if (!$this->connected) {
            return null;
        }

        try {
            $command = $this->createCommand(self::CMD_GET_TIME, 0, 0);
            $this->send($command);
            
            $response = $this->receive();
            
            if ($response && $this->getCommand($response) == self::CMD_ACK_OK) {
                $data = $this->getData($response);
                // Parse time from binary data
                // Format: YYYY-MM-DD HH:MM:SS
                if (strlen($data) >= 14) {
                    $year = substr($data, 0, 4);
                    $month = substr($data, 4, 2);
                    $day = substr($data, 6, 2);
                    $hour = substr($data, 8, 2);
                    $minute = substr($data, 10, 2);
                    $second = substr($data, 12, 2);
                    
                    return \DateTime::createFromFormat(
                        'Y-m-d H:i:s',
                        "{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}"
                    );
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error("ZKTeco getTime error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get attendance logs
     * Note: This is a simplified implementation
     * Full implementation requires proper packet handling
     */
    public function getAttendanceLogs(\DateTime $fromDate = null, \DateTime $toDate = null): array
    {
        if (!$this->connected) {
            return [];
        }

        try {
            $command = $this->createCommand(self::CMD_GET_ATTENDANCE, 0, 0);
            $this->send($command);
            
            $response = $this->receive();
            
            if ($response && $this->getCommand($response) == self::CMD_ACK_OK) {
                // Parse attendance data from response
                // This is simplified - full implementation needs proper parsing
                $data = $this->getData($response);
                return $this->parseAttendanceData($data, $fromDate, $toDate);
            }

            return [];
        } catch (\Exception $e) {
            Log::error("ZKTeco getAttendanceLogs error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if connected
     */
    public function isConnected(): bool
    {
        return $this->connected && $this->socket && !feof($this->socket);
    }

    /**
     * Create command packet
     */
    private function createCommand(int $command, int $checksum, int $sessionId, string $data = ''): string
    {
        $packet = pack('vvvv', $command, $checksum, $sessionId, 0);
        $packet .= $data;
        $packet .= pack('v', $this->calculateChecksum($packet));
        return $packet;
    }

    /**
     * Send data to device
     */
    private function send(string $data): bool
    {
        if (!$this->socket) {
            return false;
        }

        return fwrite($this->socket, $data) !== false;
    }

    /**
     * Receive data from device
     */
    private function receive(): ?string
    {
        if (!$this->socket) {
            return null;
        }

        // Read header (8 bytes)
        $header = fread($this->socket, 8);
        
        if (strlen($header) < 8) {
            return null;
        }

        $unpacked = unpack('vcommand/vchecksum/vsession/vreply', $header);
        $command = $unpacked['command'];
        $reply = $unpacked['reply'];

        // Read data if present
        $data = '';
        if ($reply > 0) {
            $data = fread($this->socket, $reply);
        }

        return $header . $data;
    }

    /**
     * Get command from response
     */
    private function getCommand(string $response): int
    {
        if (strlen($response) < 2) {
            return 0;
        }

        $unpacked = unpack('vcommand', substr($response, 0, 2));
        return $unpacked['command'];
    }

    /**
     * Get data from response
     */
    private function getData(string $response): string
    {
        if (strlen($response) < 8) {
            return '';
        }

        $unpacked = unpack('vcommand/vchecksum/vsession/vreply', substr($response, 0, 8));
        $reply = $unpacked['reply'];

        if ($reply > 0 && strlen($response) > 8) {
            return substr($response, 8, $reply);
        }

        return '';
    }

    /**
     * Calculate checksum
     */
    private function calculateChecksum(string $data): int
    {
        $checksum = 0;
        $length = strlen($data);
        
        for ($i = 0; $i < $length; $i += 2) {
            if ($i + 1 < $length) {
                $checksum += ord($data[$i]) + (ord($data[$i + 1]) << 8);
            } else {
                $checksum += ord($data[$i]);
            }
        }

        return $checksum & 0xFFFF;
    }

    /**
     * Parse attendance data
     * Simplified parser - full implementation needed
     */
    private function parseAttendanceData(string $data, \DateTime $fromDate = null, \DateTime $toDate = null): array
    {
        $logs = [];
        
        // This is a placeholder - full implementation requires:
        // 1. Proper binary parsing
        // 2. Handling multiple records
        // 3. Date filtering
        // 4. User ID mapping
        
        // For now, return empty array
        // Full implementation would parse the binary data structure
        
        return $logs;
    }

    /**
     * Destructor - ensure connection is closed
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}

