document.addEventListener('DOMContentLoaded', function() {
    // Departments data
    const departments = [
        "MOTOR",
        "ADVANCE THIRD PARTY BRONZE",
        "AGRIC",
        "ALL RISK",
        "AUTOMOBILE CONTINGENCY LIABILITY INSURANCE",
        "AVIATION",
        "BOND",
        "BUILDER LIABILITY",
        "BURGLARY",
        "BUSINESS INTERRUPTION",
        "COMBINED AGRICULTURAL PRODUCT & INVESTMENT",
        "COMMERCIAL MOTOR",
        "COMPUTER ALL RISK",
        "CONTRACTORS ALL RISK",
        "CORPORATE PROTECTION",
        "DIRECTORS & OFFICERS LIABILITY",
        "ELECTRONICS EQUIPMENT",
        "EMPLOYERS LIABILITY INSURANCE",
        "ERECTION ALL RISK",
        "FIDELITY GUARANTEE",
        "FIR_NELSON CHUKWUEBUKA OGBONNAY STANDARD CHARTERED BANK_HEO-CMT4-20251",
        "FIRE AND SPECIAL PERIL",
        "GOODS IN TRANSIT",
        "GROUP PERSONAL ACCIDENT",
        "HOME INSURANCE",
        "INVENTORY FINANCE SCHEME",
        "KIDNAPPING & RANSOM",
        "LIVESTOCK INSURANCE",
        "MACHINERY BREAKDOWN",
        "MARINE CARGO",
        "MARINE CLAUSE C",
        "MARINE HULL",
        "MARINE REPORT",
        "MOBILE DEVICE",
        "MONEY",
        "MORTGAGE FIRE INSURANCE",
        "MOTOR ( THIRD PARTY )",
        "MOTOR INSURANCE",
        "MOTORCYCLE",
        "MULTI PERIL CROP INSURANCE",
        "NIPEX AND DPR",
        "OCCUPIERS LIABILITY",
        "OIL & GAS",
        "PACKAGE",
        "PERSONAL ACCIDENT",
        "PLANT ALL RISKS",
        "PRIVATE MOTOR",
        "PROFESSIONAL INDEMNITY",
        "PUBLIC LIABILITY",
        "TERRORISM & SABOTAGE",
        "TERRORISM ENGINEERING",
        "TERRORISM FIRE",
        "TRAVEL INSURANCE",
        "TRICYCLE",
        "WORKMENS COMPENSATION",
        "XNET-TRACKING FILE_2017",
        "PACKAGE",
        "PROPOSALS FOR REINSURANCE BROKERS",
        "PROPOSALS FOR SURVEY",
        "ROOMANS PREMIUM REMITTANCE",
        "SALVAGE BUYERS",
        "SLIPS_ACKNOWLEDGEMENT SLIPS",
        "SURVEY REPORT_FEDERAL AIRPORT AUTHORITY OF NIGERIA (F.A.A.N.)",
        "SURVEY REPORT_VARIOUS MARINE",
        "TOPS",
        "VEHICLE TRACKING & SPEED LIMITER"
    ];

    // Sample documents data
    const sampleDocuments = {
        'MOTOR': [
            { id: 1, name: 'Motor Insurance ', type: 'pdf', uploadedBy: 'John Doe', date: '2023-07-15', size: '4.2 MB', url: 'sample.pdf' },
            { id: 2, name: 'Vehicle Claims Report June 2023.docx', type: 'word', uploadedBy: 'Jane Smith', date: '2023-06-30', size: '1.8 MB', url: 'sample.pdf' },
            { id: 3, name: 'Premium Calculations.xlsx', type: 'excel', uploadedBy: 'Mike Johnson', date: '2023-07-01', size: '3.5 MB', url: 'sample.pdf' }
        ],
        'NSIA_TOPS_AGRIC': [
            { id: 4, name: 'Agricultural Insurance Plan 2023.pdf', type: 'pdf', uploadedBy: 'Sarah Williams', date: '2023-06-20', size: '5.1 MB', url: 'sample.pdf' },
            { id: 5, name: 'Crop Yield Report Q2.docx', type: 'word', uploadedBy: 'Robert Brown', date: '2023-07-05', size: '2.3 MB', url: 'sample.pdf' }
        ],
        'NSIA_TOPS_ALL RISK': [
            { id: 6, name: 'All Risk Policy Template.docx', type: 'word', uploadedBy: 'Emily Davis', date: '2023-05-10', size: '1.2 MB', url: 'sample.pdf' },
            { id: 7, name: 'Risk Assessment Matrix.xlsx', type: 'excel', uploadedBy: 'David Wilson', date: '2023-07-12', size: '2.7 MB', url: 'sample.pdf' }
        ]
        // Add more sample data for other departments as needed
    };

    // Recent documents (combines all departments)
    const recentDocuments = [
        { id: 1, name: 'Motor Insurance Policy Q3 2023.pdf', department: 'MOTOR', type: 'pdf', uploadedBy: 'John Doe', date: '2023-07-15', size: '4.2 MB', url: 'sample.pdf' },
        { id: 4, name: 'Agricultural Insurance Plan 2023.pdf', department: 'AGRIC', type: 'pdf', uploadedBy: 'Sarah Williams', date: '2023-06-20', size: '5.1 MB', url: 'sample.pdf' },
        { id: 6, name: 'All Risk Policy Template.docx', department: 'ALL RISK', type: 'pdf', uploadedBy: 'Emily Davis', date: '2023-05-10', size: '1.2 MB', url: 'sample.pdf' },
        { id: 2, name: 'Vehicle Claims Report June 2023.docx', department: 'MOTOR', type: 'pdf', uploadedBy: 'Jane Smith', date: '2023-06-30', size: '1.8 MB', url: 'sample.pdf' },
        { id: 7, name: 'Risk Assessment Matrix.xlsx', department: 'ALL RISK', type: 'pdf', uploadedBy: 'David Wilson', date: '2023-07-12', size: '2.7 MB', url: 'sample.pdf' }
    ];

    // DOM Elements
    const departmentList = document.getElementById('departmentList');
    const departmentSidebar = document.getElementById('departmentSidebar');
    const toggleSidebar = document.getElementById('toggleSidebar');
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const userControls = document.getElementById('userControls');
    const userProfile = document.getElementById('userProfile');
    const displayUsername = document.getElementById('displayUsername');
    const logoutBtn = document.getElementById('logoutBtn');
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
    const showRegister = document.getElementById('showRegister');
    const showLogin = document.getElementById('showLogin');
    const currentDepartment = document.getElementById('currentDepartment');
    const dashboardView = document.getElementById('dashboardView');
    const departmentView = document.getElementById('departmentView');
    const recentDocumentsTable = document.getElementById('recentDocumentsTable');
    const newDocumentBtn = document.getElementById('newDocumentBtn');
    const mergeDocumentBtn = document.getElementById('mergeDocumentBtn');
    const newDocumentModal = new bootstrap.Modal(document.getElementById('newDocumentModal'));
    const mergeDocumentModal = new bootstrap.Modal(document.getElementById('mergeDocumentModal'));
    const documentPreviewModal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
    const pdfPreview = document.getElementById('pdfPreview');
    const downloadPreviewBtn = document.getElementById('downloadPreviewBtn');
    const previewDocumentTitle = document.getElementById('previewDocumentTitle');
    const docDepartment = document.getElementById('docDepartment');
    const sourceDepartment = document.getElementById('sourceDepartment');
    const targetDepartment = document.getElementById('targetDepartment');
    const mergeDepartment = document.getElementById('mergeDepartment');
    const sourceDocumentsList = document.getElementById('sourceDocumentsList');
    const targetDocumentsList = document.getElementById('targetDocumentsList');
    const documentListBody = document.getElementById('documentListBody');
    const departmentSearch = document.getElementById('departmentSearch');
    const documentSearch = document.getElementById('documentSearch');
    const selectAllDocuments = document.getElementById('selectAllDocuments');
    const newDocumentForm = document.getElementById('newDocumentForm');
    const mergeDocumentForm = document.getElementById('mergeDocumentForm');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Populate departments in sidebar and select dropdowns
    function populateDepartments() {
        departmentList.innerHTML = '';
        
        departments.forEach(dept => {
            // Add to sidebar
            const li = document.createElement('li');
            li.className = 'department-item';
            li.innerHTML = `<i class="fas fa-folder me-2"></i><span>${dept}</span>`;
            li.addEventListener('click', () => {
                // Remove active class from all items
                document.querySelectorAll('.department-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked item
                li.classList.add('active');
                
                // Update current department display
                currentDepartment.textContent = `${dept} Documents`;
                
                // Show department view and hide dashboard
                dashboardView.classList.add('d-none');
                departmentView.classList.remove('d-none');
                
                // Load documents for this department
                loadDepartmentDocuments(dept);
                
                // For mobile, close sidebar after selection
                if (window.innerWidth <= 768) {
                    departmentSidebar.classList.remove('show');
                }
            });
            departmentList.appendChild(li);
            
            // Add to select dropdowns
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            docDepartment.appendChild(option.cloneNode(true));
            sourceDepartment.appendChild(option.cloneNode(true));
            targetDepartment.appendChild(option.cloneNode(true));
            mergeDepartment.appendChild(option.cloneNode(true));
        });
    }

    function loadDepartmentDocuments(department) {
        const documents = sampleDocuments[department] || [];
        documentListBody.innerHTML = '';
        if (documents.length === 0) {
            documentListBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <p>No documents found in this department.</p>
                    </td>
                </tr>
            `;
            return;
        }
        documents.forEach(doc => {
            let iconClass;
            switch(doc.type) {
                case 'pdf':
                    iconClass = 'fas fa-file-pdf text-danger';
                    break;
                case 'word':
                    iconClass = 'fas fa-file-word text-primary';
                    break;
                case 'excel':
                    iconClass = 'fas fa-file-excel text-success';
                    break;
                default:
                    iconClass = 'fas fa-file';
            }
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" class="document-checkbox" data-id="${doc.id}"></td>
                <td><i class="${iconClass} me-2"></i>${doc.name}</td>
                <td>${doc.uploadedBy}</td>
                <td>${doc.date}</td>
                <td>${doc.size}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-2 preview-document" data-url="${doc.url}" data-name="${doc.name}" title="Preview">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success me-2 merge-document" data-id="${doc.id}" title="Merge Document">
                        <i class="fas fa-code-branch"></i>
                    </button>
                    <a href="${doc.url}" download="${doc.name}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-download"></i>
                    </a>
                </td>
            `;
            documentListBody.appendChild(tr);
        });
    
        
        // Add event listeners to preview buttons
        document.querySelectorAll('.preview-document').forEach(btn => {
            btn.addEventListener('click', function() {
                const docUrl = this.getAttribute('data-url');
                const docName = this.getAttribute('data-name');
                previewDocument(docUrl, docName);
            });
        });
    }

// Update the loadRecentDocuments function to include merge buttons
function loadRecentDocuments() {
    recentDocumentsTable.innerHTML = '';
    
    recentDocuments.forEach(doc => {
        // Determine icon based on file type
        let iconClass;
        switch(doc.type) {
            case 'pdf':
                iconClass = 'fas fa-file-pdf text-danger';
                break;
            case 'word':
                iconClass = 'fas fa-file-word text-primary';
                break;
            case 'excel':
                iconClass = 'fas fa-file-excel text-success';
                break;
            default:
                iconClass = 'fas fa-file';
        }
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="checkbox" class="document-checkbox" data-id="${doc.id}"></td>
            <td><i class="${iconClass} me-2"></i>${doc.name}</td>
            <td>${doc.uploadedBy}</td>
            <td>${doc.department}</td>
            <td>${doc.date}</td>
            <td>${doc.size}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-2 preview-document" data-url="${doc.url}" data-name="${doc.name}" title="Preview">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-success me-2 merge-document" data-id="${doc.id}" title="Merge Document">
                    <i class="fas fa-code-branch"></i>
                </button>
                <a href="${doc.url}" download="${doc.name}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-download"></i>
                </a>
            </td>
        `;
        recentDocumentsTable.appendChild(tr);
    });
    
    // Add event listeners to preview buttons
    document.querySelectorAll('.preview-document').forEach(btn => {
        btn.addEventListener('click', function() {
            const docUrl = this.getAttribute('data-url');
            const docName = this.getAttribute('data-name');
            previewDocument(docUrl, docName);
        });
    });
    
    // Add event listeners to merge buttons
    document.querySelectorAll('.merge-document').forEach(btn => {
        btn.addEventListener('click', function() {
            const docId = this.getAttribute('data-id');
            // Open merge modal with this document pre-selected
            mergeDocumentModal.show();
            // You can add logic here to pre-select this document
        });
    });
}

// Update the new document form submission
newDocumentForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const department = document.getElementById('docDepartment').value;
    const fileInput = document.getElementById('docFile');
    const file = fileInput.files[0];
    
    if (file) {
        // Get the original filename
        const originalFilename = file.name;
        
        // Create a new document object with the actual filename
        const newDoc = {
            id: Math.floor(Math.random() * 1000),
            name: originalFilename, // Use the actual filename
            type: file.type.includes('pdf') ? 'pdf' : 
                  file.type.includes('word') ? 'word' : 
                  file.type.includes('excel') ? 'excel' : 'file',
            uploadedBy: displayUsername.textContent,
            date: new Date().toISOString().split('T')[0],
            size: `${(file.size / (1024 * 1024)).toFixed(1)} MB`,
            url: URL.createObjectURL(file) // Create object URL for preview
        };
        
        if (!sampleDocuments[department]) {
            sampleDocuments[department] = [];
        }
        
        sampleDocuments[department].unshift(newDoc);
        
        // Add to recent documents
        recentDocuments.unshift({
            ...newDoc,
            department: department
        });
        
        // Refresh views
        if (currentDepartment.textContent.includes(department)) {
            loadDepartmentDocuments(department);
        }
        loadRecentDocuments();
        
        newDocumentModal.hide();
        newDocumentForm.reset();
    }
});
    // Preview document in modal
    function previewDocument(url, name) {
        previewDocumentTitle.textContent = name;
        pdfPreview.src = url;
        downloadPreviewBtn.href = url;
        downloadPreviewBtn.download = name;
        documentPreviewModal.show();
    }

    // Load documents when department select changes
    function setupDepartmentSelects() {
        [sourceDepartment, targetDepartment].forEach(select => {
            select.addEventListener('change', function() {
                const department = this.value;
                const documents = sampleDocuments[department] || [];
                const container = this.id === 'sourceDepartment' ? sourceDocumentsList : targetDocumentsList;
                
                container.innerHTML = '';
                
                if (documents.length === 0) {
                    container.innerHTML = '<div class="text-center py-3 text-muted">No documents in this department</div>';
                    return;
                }
                
                documents.forEach(doc => {
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    div.innerHTML = `
                        <input class="form-check-input" type="radio" name="${this.id === 'sourceDepartment' ? 'sourceDocument' : 'targetDocument'}" 
                               id="doc${doc.id}" value="${doc.id}" data-url="${doc.url}">
                        <label class="form-check-label" for="doc${doc.id}">
                            ${doc.name}
                        </label>
                    `;
                    container.appendChild(div);
                });
            });
        });
    }

    // Toggle sidebar collapse/expand
    toggleSidebar.addEventListener('click', () => {
        departmentSidebar.classList.toggle('collapsed');
    });

    // Login/Register functionality
    loginBtn.addEventListener('click', () => {
        loginModal.show();
    });

    registerBtn.addEventListener('click', () => {
        registerModal.show();
    });

    showRegister.addEventListener('click', (e) => {
        e.preventDefault();
        loginModal.hide();
        registerModal.show();
    });

    showLogin.addEventListener('click', (e) => {
        e.preventDefault();
        registerModal.hide();
        loginModal.show();
    });

    // Simulate login
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('loginEmail').value;
        const firstName = email.split('@')[0];
        displayUsername.textContent = firstName;
        userControls.classList.add('d-none');
        userProfile.classList.remove('d-none');
        loginModal.hide();
    });

    // Simulate registration
    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const firstName = document.getElementById('firstName').value;
        displayUsername.textContent = firstName;
        userControls.classList.add('d-none');
        userProfile.classList.remove('d-none');
        registerModal.hide();
    });

    // Logout
    logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();
        userControls.classList.remove('d-none');
        userProfile.classList.add('d-none');
        // Reset forms
        loginForm.reset();
        registerForm.reset();
    });

    // Document management
    newDocumentBtn.addEventListener('click', () => {
        newDocumentModal.show();
    });

    mergeDocumentBtn.addEventListener('click', () => {
        mergeDocumentModal.show();
    });

    // Handle new document form submission
    newDocumentForm.addEventListener('submit', (e) => {
        e.preventDefault();
     
        const department = document.getElementById('docDepartment').value;
        const fileInput = document.getElementById('docFile');
        const file = fileInput.files[0];
        
        if (file) {
            alert(`Document "${docName}" would be uploaded to ${department} in a real application.`);
            newDocumentModal.hide();
            newDocumentForm.reset();
            
            // Simulate adding the new document
            if (!sampleDocuments[department]) {
                sampleDocuments[department] = [];
            }
            
            const newDoc = {
                id: Math.floor(Math.random() * 1000),
                name: file.name,
                type: 'pdf',
                uploadedBy: displayUsername.textContent,
                date: new Date().toISOString().split('T')[0],
                size: `${(file.size / (1024 * 1024)).toFixed(1)} MB`,
                url: 'sample.pdf'
            };
            
            sampleDocuments[department].unshift(newDoc);
            
            // If this department is currently viewed, refresh the list
            if (currentDepartment.textContent.includes(department)) {
                loadDepartmentDocuments(department);
            }
            
            // Add to recent documents
            recentDocuments.unshift({
                ...newDoc,
                department: department
            });
            
            // Refresh recent documents table
            loadRecentDocuments();
        }
    });

    // Handle merge document form submission
    mergeDocumentForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const sourceDocId = document.querySelector('input[name="sourceDocument"]:checked')?.value;
        const targetDocId = document.querySelector('input[name="targetDocument"]:checked')?.value;
        const mergeName = document.getElementById('mergeName').value;
        const department = document.getElementById('mergeDepartment').value;
        
        if (!sourceDocId || !targetDocId) {
            alert('Please select both source and target documents');
            return;
        }
        
        // In a real app, you would merge the documents on the server here
        alert(`Documents would be merged into "${mergeName}" and saved to ${department} in a real application.`);
        mergeDocumentModal.hide();
        mergeDocumentForm.reset();
        
        // Simulate adding the merged document
        if (!sampleDocuments[department]) {
            sampleDocuments[department] = [];
        }
        
        const mergedDoc = {
            id: Math.floor(Math.random() * 1000),
            name: mergeName,
            type: 'pdf',
            uploadedBy: displayUsername.textContent,
            date: new Date().toISOString().split('T')[0],
            size: '5.0 MB', // Example size
            url: 'sample.pdf'
        };
        
        sampleDocuments[department].unshift(mergedDoc);
        
        // If this department is currently viewed, refresh the list
        if (currentDepartment.textContent.includes(department)) {
            loadDepartmentDocuments(department);
        }
        
        // Add to recent documents
        recentDocuments.unshift({
            ...mergedDoc,
            department: department
        });
        
        // Refresh recent documents table
        loadRecentDocuments();
    });

    // Department search functionality
    departmentSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = departmentList.querySelectorAll('li');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Document search functionality
    documentSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = documentListBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Select all documents checkbox
    selectAllDocuments.addEventListener('change', function() {
        const checkboxes = documentListBody.querySelectorAll('.document-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // For mobile, add a button to toggle sidebar
    if (window.innerWidth <= 768) {
        const mobileSidebarToggle = document.createElement('button');
        mobileSidebarToggle.className = 'btn btn-primary d-md-none mb-3';
        mobileSidebarToggle.innerHTML = '<i class="fas fa-bars"></i> Menu';
        mobileSidebarToggle.addEventListener('click', () => {
            departmentSidebar.classList.toggle('show');
        });
        document.querySelector('.main-content').prepend(mobileSidebarToggle);
    }

    

    // Responsive adjustments
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            departmentSidebar.classList.remove('show');
        }
    });

    // Initialize the app
    populateDepartments();
    loadRecentDocuments();
    setupDepartmentSelects();
});