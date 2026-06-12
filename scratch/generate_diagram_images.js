const zlib = require('zlib');
const http = require('https');
const fs = require('fs');
const path = require('path');

const diagrams = {
    account_manage: `sequenceDiagram
    autonumber
    actor Admin as Admin User
    participant Browser as Client Browser (JS/UI)
    participant Route as Laravel Router (web.php)
    participant UserCtrl as UserController
    participant DB as MySQL Database

    Admin->>Browser: Click "Kelola Akun" menu
    Browser->>Route: GET /users/manage
    Route->>UserCtrl: manage()
    UserCtrl->>DB: Get all users
    DB-->>UserCtrl: Return users list
    UserCtrl-->>Browser: Render users.manage view
    Browser-->>Admin: Show account grid

    alt Alternative 1: Create Account
        Admin->>Browser: Click "Tambah Akun", fill details & select Role
        Note over Browser: Role Options: Admin, Notaris, Staff, Freelancer
        Admin->>Browser: Submit Form
        Browser->>Route: POST /users
        Route->>UserCtrl: store(Request)
        UserCtrl->>DB: Save new user with hashed password
        DB-->>UserCtrl: Success
        UserCtrl-->>Browser: Redirect back with success toast
        Browser-->>Admin: Show new account card in grid
        
    else Alternative 2: Edit Account Details
        Admin->>Browser: Click "Edit" on a card & modify fields
        Admin->>Browser: Submit edit form
        Browser->>Route: PUT /users/{id}
        Route->>UserCtrl: update(Request, id)
        alt Option A: Password field is modified
            UserCtrl->>UserCtrl: Hash and update new password
        else Option B: Password field is empty
            UserCtrl->>UserCtrl: Update details only (name, email, role, etc.)
        end
        UserCtrl->>DB: Update user record
        DB-->>UserCtrl: Success
        UserCtrl-->>Browser: Redirect back with updated info toast
        Browser-->>Admin: Update account details in grid
        
    else Alternative 3: Toggle User Status (Deactivate / Restore)
        alt Option A: Deactivate Active User
            Admin->>Browser: Click "Nonaktif" button
            Browser->>Route: POST /users/{id}/deactivate (AJAX)
            Route->>UserCtrl: deactivate(id)
            UserCtrl->>DB: Update is_active = false & Soft delete
            DB-->>UserCtrl: Success
            UserCtrl-->>Browser: Return JSON Success
        else Option B: Restore Deactivated User
            Admin->>Browser: Click "Aktifkan" button
            Browser->>Route: POST /users/{id}/restore (AJAX)
            Route->>UserCtrl: restore(id)
            UserCtrl->>DB: Update is_active = true & Restore record
            DB-->>UserCtrl: Success
            UserCtrl-->>Browser: Return JSON Success
        end
        Browser->>Browser: JS reloads page
        Browser-->>Admin: Reflect updated status in grid
    end`,

    staff_manage: `sequenceDiagram
    autonumber
    actor User as Admin / Notaris User
    participant Browser as Client Browser (JS/UI)
    participant Route as Laravel Router (web.php)
    participant StaffCtrl as StaffController
    participant DB as MySQL Database

    User->>Browser: Click "Daftar Staff" menu
    Browser->>Route: GET /staff
    Route->>StaffCtrl: index()
    StaffCtrl->>DB: Query staffs list
    DB-->>StaffCtrl: Return staff data
    StaffCtrl-->>Browser: Render staff.index view
    Browser-->>User: Show staff table list

    alt Alternative 1: Add Staff (Linked vs Manual Profile)
        User->>Browser: Click "Tambah Staff" button
        Browser->>Route: GET /staff/create
        Route->>StaffCtrl: create()
        StaffCtrl->>DB: Query active users
        DB-->>StaffCtrl: Return users list
        StaffCtrl-->>Browser: Render staff.create view
        
        alt Option A: Link Staff to Existing User
            User->>Browser: Select User from dropdown
            Browser->>Browser: JS auto-fills Name, Email, Position, Work Status
        else Option B: Create Manual Profile
            User->>Browser: Leave User selection blank
            User->>Browser: Manually fill all details (Name, Position, Phone, Address, birth date)
        end
        
        User->>Browser: Click "Simpan Data Staff"
        Browser->>Route: POST /staff
        Route->>StaffCtrl: store(Request)
        StaffCtrl->>DB: Save new staff record
        DB-->>StaffCtrl: Success
        StaffCtrl-->>Browser: Redirect to staff.index with success alert
        Browser-->>User: Show updated staff list
        
    else Alternative 2: Edit Staff Details
        User->>Browser: Click "Edit" on staff row
        Browser->>Route: GET /staff/{id}/edit
        Route->>StaffCtrl: edit(staff)
        StaffCtrl->>DB: Query staff data
        DB-->>StaffCtrl: Return data
        StaffCtrl-->>Browser: Render staff.edit view
        User->>Browser: Modify fields & Click Save
        Browser->>Route: PUT /staff/{id}
        Route->>StaffCtrl: update(Request, staff)
        StaffCtrl->>DB: Update staff record
        DB-->>StaffCtrl: Success
        StaffCtrl-->>Browser: Redirect to staff.index with success alert
        Browser-->>User: Show updated table
    end`,

    case_calendar: `sequenceDiagram
    autonumber
    actor OpUser as Operator (Notaris / Staff / Freelancer)
    participant Browser as Client Browser (JS/UI)
    participant Route as Laravel Router (web.php)
    participant CaseCtrl as CaseController
    participant DB as MySQL Database

    OpUser->>Browser: Click "Case Calendar" menu
    Browser->>Route: GET /cases/calendar
    Route->>CaseCtrl: calendar()
    CaseCtrl->>DB: Query cases (deadline, status, type) & clients (birth_date)
    DB-->>CaseCtrl: Return collections
    CaseCtrl-->>Browser: Render cases.calendar view
    Browser->>Browser: JS renderCal() displays calendar grid
    Browser-->>OpUser: Show calendar month view

    alt Alternative 1: Click date with Client Birthday
        OpUser->>Browser: Click date containing 🎂 pin
        Browser->>Browser: JS showDetail(dateStr)
        Browser->>Browser: Render client birthday card (pink theme, phone details, birth date)
        Browser-->>OpUser: Display birthday info under calendar
    else Alternative 2: Click date with Case Deadlines
        OpUser->>Browser: Click date containing Case status pin
        Browser->>Browser: JS showDetail(dateStr)
        Browser->>Browser: Render case card (status theme: selesai=green, proses=amber, tertunda=red)
        Browser-->>OpUser: Display case details (case name, client name, type, phone, deadline) under calendar
    else Alternative 3: Click date with Both Birthday & Case
        OpUser->>Browser: Click date containing both pins
        Browser->>Browser: JS showDetail(dateStr)
        Browser->>Browser: Render both birthday cards and case cards
        Browser-->>OpUser: Display unified list under calendar
    else Alternative 4: Click Empty Date
        OpUser->>Browser: Click date without any events
        Browser->>Browser: JS showDetail(dateStr)
        Browser->>Browser: Render empty state text ("Tidak ada kasus atau hari ulang tahun...")
        Browser-->>OpUser: Show empty details message
    end`
};

const outputDir = path.join(__dirname, '../public/images');
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

console.log('Generating PNG images of sequence diagrams...');

const keys = Object.keys(diagrams);
let currentIndex = 0;

function processNext() {
    if (currentIndex >= keys.length) {
        console.log('All images generated successfully!');
        return;
    }

    const name = keys[currentIndex];
    const code = diagrams[name];
    
    const compressed = zlib.deflateSync(code, { level: 9 });
    const base64url = compressed.toString('base64url');
    const url = `https://kroki.io/mermaid/png/${base64url}`;
    
    const outputPath = path.join(outputDir, `${name}_sequence.png`);
    console.log(`Fetching ${name} from Kroki...`);
    
    http.get(url, (res) => {
        if (res.statusCode === 200) {
            const file = fs.createWriteStream(outputPath);
            res.pipe(file);
            file.on('finish', () => {
                console.log(`Saved ${name}_sequence.png to: ${outputPath}`);
                currentIndex++;
                processNext();
            });
        } else {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => {
                console.error(`Failed to fetch ${name} (Status: ${res.statusCode}):`, data);
                currentIndex++;
                processNext();
            });
        }
    }).on('error', (err) => {
        console.error(`Network error on ${name}:`, err);
        currentIndex++;
        processNext();
    });
}

processNext();
