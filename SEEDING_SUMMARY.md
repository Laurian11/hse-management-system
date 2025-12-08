# Company & Department Seeding Summary

**Date:** December 2025  
**Seeder:** `CompanyDepartmentSeeder`

---

## ✅ Seeded Companies

### 1. Tanzania Construction Ltd
- **Industry Type:** Construction
- **Employees:** 350
- **Location:** Dar es Salaam, Tanzania
- **Email:** admin@tanzaniaconstruction.co.tz
- **Password:** password

**Departments (10):**
1. Project Management (PM)
2. Site Operations (SO)
3. Safety & Health (SH)
4. Engineering (ENG)
5. Procurement (PROC)
6. Quality Control (QC)
7. Human Resources (HR)
8. Finance (FIN)
9. Maintenance (MAINT)
10. Logistics (LOG)

---

### 2. ICD Industrial Services Ltd
- **Industry Type:** Industrial
- **Employees:** 200
- **Location:** Dar es Salaam, Tanzania
- **Email:** admin@icdindustrial.co.tz
- **Password:** password

**Departments (10):**
1. Industrial Control (IC)
2. Project Engineering (PE)
3. Safety & Compliance (SC)
4. Operations (OPS)
5. Maintenance (MAINT)
6. Procurement (PROC)
7. Quality Assurance (QA)
8. Human Resources (HR)
9. Finance (FIN)
10. IT & Systems (IT)

---

### 3. Tanzania Transport Services Ltd
- **Industry Type:** Transportation
- **Employees:** 280
- **Location:** Dar es Salaam, Tanzania
- **Email:** admin@tanzaniatransport.co.tz
- **Password:** password

**Departments (10):**
1. Fleet Management (FM)
2. Logistics (LOG)
3. Safety & Compliance (SC)
4. Operations (OPS)
5. Maintenance (MAINT)
6. Dispatch (DISP)
7. Customer Service (CS)
8. Human Resources (HR)
9. Finance (FIN)
10. Warehouse (WH)

---

## 📊 Summary

- **Total Companies:** 3
- **Total Departments:** 30 (10 per company)
- **Total Admin Users:** 3 (1 per company)

---

## 🔐 Login Credentials

| Company | Email | Password |
|---------|-------|----------|
| Construction | admin@tanzaniaconstruction.co.tz | password |
| ICD | admin@icdindustrial.co.tz | password |
| Transportation | admin@tanzaniatransport.co.tz | password |

---

## 🚀 Usage

To run the seeder again:
```bash
php artisan db:seed --class=CompanyDepartmentSeeder
```

To reset and reseed:
```bash
php artisan migrate:fresh --seed
```

---

**Seeding Complete!** ✅

