# Automation Testing Guide

**Date:** December 2025  
**System:** HSE Management System

---

## 🧪 Complete Testing Checklist

### 1. Procurement → Stock → PPE Automation Test

**Steps:**
1. Login to the system
2. Navigate to: `http://127.0.0.1:8000/procurement/requests/create`
3. Fill in the form:
   - Item Name: "Test Safety Helmet"
   - Category: "PPE"
   - Quantity: 50
   - Unit: "pieces"
   - Estimated Cost: 500000
   - Priority: High
4. Click "Create Request"
5. Go to the created request: `http://127.0.0.1:8000/procurement/requests/{id}`
6. Click "Edit"
7. Update status to: "received"
8. Fill in:
   - Purchase Cost: 500000
   - Received Date: Today
   - Supplier: (select one if available)
9. Click "Update Request"

**Expected Results:**
- ✅ PPE item automatically created in stock
- ✅ Navigate to: `http://127.0.0.1:8000/ppe/items`
- ✅ Find "Test Safety Helmet" in the list
- ✅ Quantity should be 50
- ✅ Reference number should be generated
- ✅ QR code should be available

**Verification:**
- Check PPE items: `http://127.0.0.1:8000/ppe/items`
- Look for item named "Test Safety Helmet"
- Verify quantity matches procurement request

---

### 2. Supplier Suggestions Test

**Steps:**
1. Navigate to: `http://127.0.0.1:8000/procurement/requests/create`
2. Select Category: "PPE"
3. Reload the page (or submit form and go back)
4. Check if suggested suppliers appear below the form

**Expected Results:**
- ✅ Suggested suppliers box appears
- ✅ Shows suppliers with type "PPE" or "other"
- ✅ Can click on supplier to select it

**Verification:**
- Create a supplier with type "PPE" first if none exist
- Navigate to: `http://127.0.0.1:8000/procurement/suppliers/create`
- Create supplier with type "PPE"
- Then test the suggestions

---

### 3. Auto Email Notifications Test

**Prerequisites:**
- Configure email in `.env`:
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.mailtrap.io
  MAIL_PORT=2525
  MAIL_USERNAME=your_username
  MAIL_PASSWORD=your_password
  ```

- Configure procurement notifications in `config/procurement.php`:
  ```php
  'auto_send_notifications' => true,
  'notification_emails' => 'test@example.com',
  ```

**Steps:**
1. Create a procurement request
2. Update status from "draft" to "submitted"
3. Check email inbox (or mailtrap)
4. Update status to "approved"
5. Check email again
6. Update status to "received"
7. Check email again

**Expected Results:**
- ✅ Email sent when status changes to "submitted"
- ✅ Email sent when status changes to "approved"
- ✅ Email sent when status changes to "received"
- ✅ Requester receives email on status changes

**Verification:**
- Check mailtrap inbox or configured email
- Verify email content includes request details

---

### 4. QR Code System Test

**Steps:**
1. Navigate to: `http://127.0.0.1:8000/ppe/items`
2. Click on any PPE item
3. Look for "Print QR Code" button
4. Click "Print QR Code"
5. Verify printable labels appear (30 per page)
6. Test QR code scan:
   - Navigate to: `http://127.0.0.1:8000/qr/ppe/{item_id}`
   - Should show item details
   - Test with action: `http://127.0.0.1:8000/qr/ppe/{item_id}?action=check`
   - Should update system

**Expected Results:**
- ✅ QR code displayed on item page
- ✅ Printable format shows 30 labels per page
- ✅ QR code scan shows item details
- ✅ Scan with action updates system

**Verification:**
- Check PPE item show page for QR code
- Test printable format
- Scan QR code with mobile device

---

### 5. Email Sharing Test

**Steps:**
1. Navigate to any document/report page
2. Look for "Share via Email" button (if component is added)
3. Or manually test: `http://127.0.0.1:8000/email/share` (POST)
4. Fill in:
   - Recipients: "test1@example.com, test2@example.com"
   - Subject: "Test Email Share"
   - Content: "This is a test email"
   - Attachments: (optional file upload)
5. Submit

**Expected Results:**
- ✅ Email sent to all recipients
- ✅ Attachments included (if provided)
- ✅ Success message displayed

**Verification:**
- Check email inbox
- Verify attachments received

---

### 6. Toolbox Attendance - Multiple Names Test

**Steps:**
1. Navigate to: `http://127.0.0.1:8000/toolbox-talks`
2. Create or open a toolbox talk
3. Go to attendance management
4. Click "Multiple Employees" tab
5. Enter names: "John Doe, Jane Smith, Bob Johnson"
   (Use actual employee names from your system)
6. Select status: "Present"
7. Click "Mark Attendance"

**Expected Results:**
- ✅ All found employees marked as present
- ✅ Success message shows count of marked employees
- ✅ Not found names reported (if any)
- ✅ Attendance records created

**Verification:**
- Check attendance list
- Verify all employees marked
- Check statistics updated

---

## 🔍 Automated Verification

Run the test script:
```bash
php test-automation.php
```

This will automatically test:
- Procurement → PPE automation
- Supplier suggestions
- QR code generation
- Observer registration
- Email share controller
- Toolbox attendance enhancement

---

## 📊 Test Results Template

```
Test Date: ___________
Tester: ___________

1. Procurement → PPE Automation: [ ] PASS [ ] FAIL
   Notes: _________________________________

2. Supplier Suggestions: [ ] PASS [ ] FAIL
   Notes: _________________________________

3. Auto Email Notifications: [ ] PASS [ ] FAIL
   Notes: _________________________________

4. QR Code System: [ ] PASS [ ] FAIL
   Notes: _________________________________

5. Email Sharing: [ ] PASS [ ] FAIL
   Notes: _________________________________

6. Toolbox Attendance (Multiple): [ ] PASS [ ] FAIL
   Notes: _________________________________
```

---

## ⚠️ Common Issues & Solutions

### Issue: PPE item not created
**Solution:** 
- Check observer is registered in `AppServiceProvider`
- Verify status is exactly "received"
- Check category is "ppe" or "safety_equipment"

### Issue: Email not sending
**Solution:**
- Verify `.env` email configuration
- Check `config/procurement.php` settings
- Test email connection: `php artisan tinker` → `Mail::raw('test', function($m) { $m->to('test@example.com')->subject('test'); });`

### Issue: QR code not displaying
**Solution:**
- Check internet connection (uses external API)
- Verify QR code service is working
- Check item has reference number

### Issue: Supplier suggestions not showing
**Solution:**
- Create suppliers with matching types
- Reload page after selecting category
- Check browser console for errors

---

**Ready for Testing!** 🚀

