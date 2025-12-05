<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecentItemsController extends Controller
{
    /**
     * Track a recently viewed item
     */
    public function track(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string',
            'module' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
        ]);
        
        $recentItems = session('recent_items', []);
        
        // Remove if already exists (to move to top)
        $recentItems = array_filter($recentItems, function($item) use ($request) {
            return $item['url'] !== $request->url;
        });
        
        // Add new item at the beginning
        array_unshift($recentItems, [
            'title' => $request->title,
            'url' => $request->url,
            'module' => $request->module ?? 'General',
            'icon' => $request->icon ?? 'fa-file',
            'viewed_at' => now()->toIso8601String(),
        ]);
        
        // Keep only last 20 items
        $recentItems = array_slice($recentItems, 0, 20);
        
        session(['recent_items' => $recentItems]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Clear all recent items
     */
    public function clear()
    {
        session()->forget('recent_items');
        
        return response()->json(['success' => true]);
    }
}

