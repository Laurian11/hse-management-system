# Manual Testing Checklist - All Automation Features

**Date:** December 2025  
**Tester:** _______________  
**System:** HSE Management System

---

## ✅ Test 1: Procurement → Stock → PPE Automation

### Setup:
- [ ] Login to system
- [ ] Navigate to Procurement Requests

### Test Steps:
1. [ ] Go to: `http://127.0.0.1:8000/procurement/requests/create`
2. [ ] Fill form:
   - Item Name: "Test Safety Helmet"
   - Category: **PPE** (important!)
   - Quantity: 50
   - Unit: pieces
   - Estimated Cost: 500000
   - Priority: High
3. [ ] Click "Create Request"
4. [ ] Note the reference number: _______________
5. [ ] Go to the created request
6. [ ] Click "Edit"
7. [ ] Change status to: **"received"**
8. [ ] Fill in:
   - Purchase Cost: 500000
   - Received Date: Today
   - Supplier: (select if available)
9. [ ] Click "Update Request"

### Verification:
- [ ] Navigate to: `http://127.0.0.1:8000/ppe/items`
- [ ] Find "Test Safety Helmet" in the list
- [ ] Verify quantity = 50
- [ ] Verify reference number exists
- [ ] Click on the item
- [ ] Verify QR code button is visible

### Result: [ ] PASS [ ] FAIL
**Notes:** _________________________________

---

## ✅ Test 2: Supplier Suggestions

### Setup:
- [ ] Create at least one supplier with type "PPE"
- [ ] Navigate to: `http://127.0.0.1:8000/procurement/suppliers/create`
- [ ] Create supplier:
   - Name: "Test PPE Supplier"
   - Type: **PPE**
   - Status: Active

### Test Steps:
1. [ ] Go to: `http://127.0.0.1:8000/procurement/requests/create`
2. [ ] Select Category: **"PPE"**
3. [ ] Reload page (or submit and go back)
4. [ ] Check for "Suggested Suppliers" box below form

### Verification:
- [ ] Suggested suppliers box appears
- [ ] Shows "Test PPE Supplier"
- [ ] Can click supplier to select it

### Result: [ ] PASS [ ] FAIL
**Notes:** _________________________________

---

## ✅ Test 3: Auto Email Notifications

### Prerequisites:
- [ ] Configure `.env` email settings
- [ ] Configure `config/procurement.php`:
   ```php
   'auto_send_notifications' => true,
   'notification_emails' => 'your-email@example.com',
   ```

### Test Steps:
1. [ ] Create procurement request
2. [ ] Update status: draft → **submitted**
3. [ ] Check email inbox
4. [ ] Update status: submitted → **approved**
5. [ ] Check email inbox
6. [ ] Update status: approved → **received**
7. [ ] Check email inbox

### Verification:
- [ ] Email received when status = "submitted"
- [ ] Email received when status = "approved"
- [ ] Email received when status = "received"
- [ ] Email content includes request details

### Result: [ ] PASS [ ] FAIL
**Notes:** _________________________________

---

## ✅ Test 4: QR Code System

### Test Steps:
1. [ ] Navigate to: `http://127.0.0.1:8000/ppe/items`
2. [ ] Click on any PPE item
3. [ ] Look for "Print QR Code" button
4. [ ] Click "Print QR Code"
5. [ ] Verify printable page shows 30 labels
6. [ ] Test QR code URL: `http://127.0.0.1:8000/qr/ppe/{item_id}`
7. [ ] Test with action: `http://127.0.0.1:8000/qr/ppe/{item_id}?action=check`

### Verification:
- [ ] QR code displayed on item page
- [ ] Printable format shows 30 labels per page
- [ ] QR code scan page loads correctly
- [ ] Scan with action updates system

### Result: [ ] PASS [ ] FAIL
**Notes:** _________________________________

---

## ✅ Test 5: Email Sharing

### Test Steps:
1. [ ] Navigate to any document/report page
2. [ ] Look for "Share via Email" button
3. [ ] Click button
4. [ ] Fill in modal:
   - Recipients: "test1@example.com, test2@example.com"
   - Subject: "Test Email Share"
   - Content: "This is a test email"
   - Attachments: (upload a test file)
5. [ ] Click "Send Email"

### Verification:
- [ ] Success message displayed
- [ ] Email sent to all recipients
- [ ] Attachments included (if provided)

### Result: [ ] PASS [ ] FAIL
**Notes:** _________________________________

---

## ✅ Test 6: Toolbox Attendance - Multiple Names

### Setup:
- [ ] Ensure you have employees in the system
- [ ] Create or open a toolbox talk

### Test Steps:
1. [ ] Navigate to: `http://127.0.0.1:8000/toolbox-talks`
2. [ ] Create or open a toolbox talk
3. [ ] Go to attendance management
4. [ ] Click **"Multiple Employees"** tab
5. [ ] Enter names (use actual employee names):
   - Example: "John Doe, Jane Smith, Bob Johnson"
6. [ ] Select status: **"Present"**
7. [ ] Click "Mark Attendance"

### Verification:
- [ ] Success message shows count
- [ ] All found employees marked as present
- [ ] Not found names reported (if any)
- [ ] Attendance list updated
- [ ] Statistics updated

### Result: [ ] PASS [ ] FAIL
**Notes:** _________________________________

---

## 📊 Overall Test Results

**Total Tests:** 6  
**Passed:** _____  
**Failed:** _____  

**Overall Status:** [ ] ALL PASS [ ] SOME FAIL

**Issues Found:**
1. _________________________________
2. _________________________________
3. _________________________________

---

## 🔧 Quick Test Commands

### Test Procurement Automation:
```bash
# Create test procurement request via tinker
php artisan tinker
$request = App\Models\ProcurementRequest::create([...]);
$request->update(['status' => 'received']);
# Check if PPE item created
App\Models\PPEItem::where('name', 'Test Item')->first();
```

### Test QR Code:
```bash
# Generate QR code URL
php artisan tinker
$item = App\Models\PPEItem::first();
\App\Services\QRCodeService::forPPEItem($item->id, $item->reference_number, 'check');
```

### Test Email:
```bash
# Test email configuration
php artisan tinker
Mail::raw('Test email', function($m) { 
    $m->to('test@example.com')->subject('Test'); 
});
```

---

**Testing Complete!** ✅

