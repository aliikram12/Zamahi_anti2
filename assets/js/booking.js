/**
 * ZAMAHI Luxury Catering - Booking Form Controller
 * Multi-step navigation, menu logic, form validation, submission
 */

(function() {
    'use strict';
    
    // Global state
    window.currentStep = 1;
    window.totalSteps = 3;
    window.currentDishType = 'single';
    window.selectedProteins = new Set();
    
    // Event categories and their sub-categories
    window.eventSubCategories = {
        'Wedding': ['Barat', 'Walima', 'Mehndi', 'Nikah', 'Ring Ceremony', 'Bridal Shower', 'Other'],
        'Parties & Celebrations': ['Birthday' ,'Anniversary', 'Family Gatherings', 'Graduation Ceremony', 'Farewell ', 'Pool Party', 'Baby Shower', 'Other'],
        'Events': ['Office Meetings', 'Product Launch', 'Seminars & Workshops', 'Conferences & Award', 'Company Anniversary', 'Other'],
        'Other': ['Other']
    };
    
    // Protein dishes mapping
    window.proteinDishes = {
        'Chicken': [
            { name: 'Red Masala Chicken', desc: 'Succulent chicken in rich red masala sauce', hasSpice: true },
            { name: 'White Korma Chicken', desc: 'Creamy white korma with aromatic spices', hasSpice: true },
            { name: 'Grilled Chicken', desc: 'Perfectly grilled chicken with herbs', hasSpice: false }
        ],
        'Lamb': [
            { name: 'Lamb Karahi', desc: 'Tender lamb cooked in traditional karahi', hasSpice: true },
            { name: 'Lamb Biryani Style', desc: 'Aromatic lamb with biryani spices', hasSpice: true },
            { name: 'Roasted Lamb', desc: 'Slow-roasted lamb with rosemary', hasSpice: false }
        ],
        'Beef': [
            { name: 'Beef Nihari', desc: 'Slow-cooked beef nihari with rich gravy', hasSpice: true },
            { name: 'Beef Keema', desc: 'Minced beef with peas and spices', hasSpice: true }
        ],
        'BBQ': [
            { name: 'BBQ Mixed Grill', desc: 'Selection of grilled meats with BBQ glaze', hasSpice: false },
            { name: 'BBQ Seekh Kebab', desc: 'Chargrilled minced meat kebabs', hasSpice: true }
        ],
        'Vegetarian': [
            { name: 'Paneer Tikka Masala', desc: 'Cottage cheese in tikka sauce', hasSpice: true },
            { name: 'Mixed Vegetable Curry', desc: 'Seasonal vegetables in aromatic curry', hasSpice: true }
        ],
        'Vegan': [
            { name: 'Vegan Chickpea Curry', desc: 'Hearty chickpea curry with coconut', hasSpice: true },
            { name: 'Vegan Lentil Daal', desc: 'Rich lentil daal with cumin tempering', hasSpice: true }
        ]
    };
    
    console.log('[ZAMAHI] Booking JS initialized');
})();

/* ═══════════════ TIME PICKER FUNCTIONS ═══════════════ */
function generateHours() {
    const hours = [];
    for (let i = 0; i < 24; i++) {
        hours.push(i.toString().padStart(2, '0'));
    }
    return hours;
}

function generateMinutes() {
    const minutes = [];
    for (let i = 0; i < 60; i++) {
        minutes.push(i.toString().padStart(2, '0'));
    }
    return minutes;
}

function initTimePicker(containerId, hiddenInputName) {
    console.log('[TimePicker] Initializing:', containerId);
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('[TimePicker] Container not found:', containerId);
        return;
    }

    const hours = generateHours();
    const minutes = generateMinutes();

    // Wrapper for styling
    const wrapper = document.createElement('div');
    wrapper.style.display = 'flex';
    wrapper.style.alignItems = 'center';
    wrapper.style.gap = '8px';
    wrapper.style.flex = '1';

    // Create hour select - light styling
    const hourSelect = document.createElement('select');
    hourSelect.className = 'form-control time-select';
    hourSelect.style.width = '70px';
    hourSelect.style.padding = '12px';
    hourSelect.style.fontSize = '1rem';
    hourSelect.style.backgroundColor = '#FFFFFF';
    hourSelect.style.color = '#1C1C1C';
    hourSelect.style.border = '1px solid rgba(0,0,0,0.15)';
    hourSelect.style.borderRadius = '8px';
    hourSelect.style.cursor = 'pointer';
    
    const hourDefault = document.createElement('option');
    hourDefault.value = '';
    hourDefault.textContent = 'HH';
    hourDefault.disabled = true;
    hourDefault.selected = true;
    hourSelect.appendChild(hourDefault);

    hours.forEach(h => {
        const opt = document.createElement('option');
        opt.value = h;
        opt.textContent = h;
        hourSelect.appendChild(opt);
    });

    // Colon separator
    const colon = document.createElement('span');
    colon.textContent = ':';
    colon.style.fontSize = '1.2rem';
    colon.style.fontWeight = 'bold';
    colon.style.color = '#1C1C1C';

    // Create minute select - light styling
    const minuteSelect = document.createElement('select');
    minuteSelect.className = 'form-control time-select';
    minuteSelect.style.width = '70px';
    minuteSelect.style.padding = '12px';
    minuteSelect.style.fontSize = '1rem';
    minuteSelect.style.backgroundColor = '#FFFFFF';
    minuteSelect.style.color = '#1C1C1C';
    minuteSelect.style.border = '1px solid rgba(0,0,0,0.15)';
    minuteSelect.style.borderRadius = '8px';
    minuteSelect.style.cursor = 'pointer';
    
    const minuteDefault = document.createElement('option');
    minuteDefault.value = '';
    minuteDefault.textContent = 'MM';
    minuteDefault.disabled = true;
    minuteDefault.selected = true;
    minuteSelect.appendChild(minuteDefault);

    minutes.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m;
        opt.textContent = m;
        minuteSelect.appendChild(opt);
    });

    // Create hidden input
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = hiddenInputName;
    hiddenInput.id = hiddenInputName;

    function updateTimeValue() {
        if (hourSelect.value && minuteSelect.value) {
            hiddenInput.value = hourSelect.value + ':' + minuteSelect.value;
            console.log('[TimePicker] Value set to:', hiddenInput.value);
        } else {
            hiddenInput.value = '';
        }
    }

    hourSelect.addEventListener('change', updateTimeValue);
    minuteSelect.addEventListener('change', updateTimeValue);

    // Clear button - styled to match
    const clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.innerHTML = '<i class="fas fa-times"></i>';
    clearBtn.style.padding = '10px 14px';
    clearBtn.style.background = 'transparent';
    clearBtn.style.border = '1px solid rgba(0,0,0,0.15)';
    clearBtn.style.color = '#666666';
    clearBtn.style.borderRadius = '8px';
    clearBtn.style.cursor = 'pointer';
    clearBtn.title = 'Clear time';
    clearBtn.addEventListener('click', function() {
        hourSelect.value = '';
        minuteSelect.value = '';
        hiddenInput.value = '';
    });

    // Append to wrapper, then to container
    wrapper.appendChild(hourSelect);
    wrapper.appendChild(colon);
    wrapper.appendChild(minuteSelect);
    wrapper.appendChild(hiddenInput);
    wrapper.appendChild(clearBtn);
    
    container.innerHTML = '';
    container.appendChild(wrapper);
    
    console.log('[TimePicker] Initialized successfully');
}

/* ═══════════════ STEP NAVIGATION ═══════════════ */
function nextStep(current) {
    console.log('[Navigation] nextStep called:', current);
    if (!validateStep(current)) {
        console.log('[Navigation] Validation failed');
        return;
    }
    if (current < window.totalSteps) {
        goToStep(current + 1);
    }
}

function prevStep(current) {
    console.log('[Navigation] prevStep called:', current);
    if (current > 1) {
        goToStep(current - 1);
    }
}

function goToStep(step) {
    console.log('[Navigation] goToStep:', step);
    const currentEl = document.getElementById('step' + window.currentStep);
    const targetEl = document.getElementById('step' + step);
    
    if (!currentEl || !targetEl) {
        console.error('[Navigation] Step elements not found');
        return;
    }
    
    currentEl.classList.remove('active');
    targetEl.classList.add('active');
    window.currentStep = step;
    updateProgressBar();

    if (step === 6) {
        updateSummary();
        updateCalculator();
    }

    // Scroll to form top
    const formContainer = document.querySelector('.booking-form-container');
    if (formContainer) {
        formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function updateProgressBar() {
    document.querySelectorAll('.stepper-step').forEach(function(el, i) {
        const stepNum = i + 1;
        el.classList.remove('active', 'completed');
        if (stepNum === window.currentStep) {
            el.classList.add('active');
        } else if (stepNum < window.currentStep) {
            el.classList.add('completed');
        }
    });
}

/* ═══════════════ DISH TYPE MODAL ═══════════════ */
function showDishTypeModal() {
    console.log('[DishType] showDishTypeModal called');
    
    // Ensure time picker is initialized
    const timeContainer = document.getElementById('eventTimeContainer');
    if (timeContainer && timeContainer.children.length === 0) {
        console.log('[DishType] Initializing time picker');
        initTimePicker('eventTimeContainer', 'event_time');
    }
    
    // Validate Step 1 first
    if (!validateStep(1)) {
        console.log('[DishType] Validation failed, not showing modal');
        return;
    }
    
    // Show the modal
    const modal = document.getElementById('dishTypeModal');
    if (modal) {
        modal.classList.add('active');
        console.log('[DishType] Modal shown');
    } else {
        console.error('[DishType] Modal not found');
        // Fallback: directly go to step 2
        selectDishType('single');
    }
}

function closeDishTypeModal() {
    const modal = document.getElementById('dishTypeModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function selectDishType(type) {
    console.log('[DishType] selectDishType:', type);
    window.currentDishType = type;
    closeDishTypeModal();
    
    const dishTypeInput = document.getElementById('dishTypeInput');
    if (dishTypeInput) {
        dishTypeInput.value = type;
    }
    
    const singleSection = document.getElementById('singleDishSection');
    const multipleSection = document.getElementById('multipleDishesSection');
    const menuSubtitle = document.getElementById('menuSubtitle');
    
    if (type === 'single') {
        if (singleSection) singleSection.style.display = 'block';
        if (multipleSection) multipleSection.style.display = 'none';
        if (menuSubtitle) menuSubtitle.textContent = 'Select your protein and build your perfect menu.';
    } else {
        if (singleSection) singleSection.style.display = 'none';
        if (multipleSection) multipleSection.style.display = 'block';
        if (menuSubtitle) menuSubtitle.textContent = 'Select multiple proteins and build your perfect menu.';
    }
    
    goToStep(2);
}

/* ═══════════════ EVENT CATEGORY & SUB-CATEGORY ═══════════════ */

// Handle Event Category change - shows "Other" input if needed
function handleEventCategoryChange() {
    console.log('[Category] handleEventCategoryChange called');
    const categorySelect = document.getElementById('eventCategory');
    const otherCategoryContainer = document.getElementById('otherCategoryContainer');
    const otherCategoryInput = document.getElementById('eventCategoryOther');
    const typeSelect = document.getElementById('eventType');
    
    if (!categorySelect) return;
    
    const selectedCategory = categorySelect.value;
    
    // Show/hide category "Other" input
    if (otherCategoryContainer) {
        if (selectedCategory === 'Other') {
            otherCategoryContainer.style.display = 'block';
            if (otherCategoryInput) otherCategoryInput.required = true;
        } else {
            otherCategoryContainer.style.display = 'none';
            if (otherCategoryInput) {
                otherCategoryInput.required = false;
                otherCategoryInput.value = '';
            }
        }
    }
    
    // Update subcategories
    updateSubCategories();
}

// Handle Event Type change - shows "Other" input if needed
function handleEventTypeChange() {
    console.log('[Type] handleEventTypeChange called');
    const typeSelect = document.getElementById('eventType');
    const otherEventContainer = document.getElementById('otherEventContainer');
    const otherEventInput = document.getElementById('eventTypeOther');
    
    if (!typeSelect) return;
    
    const selectedType = typeSelect.value;
    
    // Show/hide type "Other" input
    if (otherEventContainer) {
        if (selectedType === 'Other') {
            otherEventContainer.style.display = 'block';
            if (otherEventInput) otherEventInput.required = true;
        } else {
            otherEventContainer.style.display = 'none';
            if (otherEventInput) {
                otherEventInput.required = false;
                otherEventInput.value = '';
            }
        }
    }
}

function updateSubCategories() {
    console.log('[SubCategory] updateSubCategories called');
    const categorySelect = document.getElementById('eventCategory');
    const typeSelect = document.getElementById('eventType');
    const otherContainer = document.getElementById('otherEventContainer');
    const otherInput = document.getElementById('eventTypeOther');
    
    if (!categorySelect || !typeSelect) {
        console.error('[SubCategory] Required elements not found');
        return;
    }
    
    const selectedCategory = categorySelect.value;
    console.log('[SubCategory] Selected category:', selectedCategory);
    
    // Reset sub-category dropdown
    typeSelect.innerHTML = '<option value="">Select event type</option>';
    typeSelect.disabled = true;
    
    // Hide other input initially
    if (otherContainer) otherContainer.style.display = 'none';
    if (otherInput) {
        otherInput.required = false;
        otherInput.value = '';
    }
    typeSelect.required = true;
    
    // Check if category has subcategories
    if (selectedCategory && selectedCategory !== 'Other' && window.eventSubCategories && window.eventSubCategories[selectedCategory]) {
        console.log('[SubCategory] Populating subcategories for:', selectedCategory);
        typeSelect.disabled = false;
        
        const subCategories = window.eventSubCategories[selectedCategory];
        subCategories.forEach(function(subCat) {
            const option = document.createElement('option');
            option.value = subCat;
            option.textContent = subCat;
            typeSelect.appendChild(option);
        });
    }
    
    // Trigger change event for validation
    typeSelect.dispatchEvent(new Event('change'));
}

/* ═══════════════ VENUE TYPE SELECTION ═══════════════ */
function selectVenueType(btn) {
    console.log('[VenueType] selectVenueType called');
    if (!btn) return;
    
    const parent = btn.closest('.spice-selector');
    if (!parent) {
        console.error('[VenueType] Parent not found');
        return;
    }
    
    parent.querySelectorAll('.spice-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    
    // Update hidden input
    const hiddenInput = document.querySelector('input[name="indoor_outdoor"]');
    if (hiddenInput) {
        hiddenInput.value = btn.dataset.value;
        console.log('[VenueType] Set indoor_outdoor to:', btn.dataset.value);
    }
}

/* ═══════════════ PROTEIN SELECTION (Single Dish) ═══════════════ */
function selectProtein(card, proteinType) {
    console.log('[Protein] selectProtein:', proteinType);
    const proteinGrid = document.getElementById('proteinGrid');
    if (!proteinGrid) return;
    
    // Deselect all
    proteinGrid.querySelectorAll('.option-card').forEach(function(c) {
        c.classList.remove('selected');
        c.querySelector('input').checked = false;
    });
    
    // Select clicked
    card.classList.add('selected');
    card.querySelector('input').checked = true;
    
    // Show dish options
    showProteinDishes(proteinType);
}

function showProteinDishes(proteinType) {
    console.log('[Protein] showProteinDishes:', proteinType);
    const container = document.getElementById('proteinDishes');
    const dishOptions = document.getElementById('dishOptions');
    const spiceGroup = document.getElementById('spiceLevelGroup');
    
    if (!container) return;
    
    const dishes = window.proteinDishes[proteinType] || [];
    let hasSpiceOption = false;
    
    let html = '';
    dishes.forEach(function(dish) {
        html += '<div class="option-card" onclick="toggleProteinDish(this)">' +
            '<input type="checkbox" name="protein_dishes[]" value="' + dish.name + '" data-has-spice="' + dish.hasSpice + '">' +
            '<span class="checkmark"></span>' +
            '<div class="option-info">' +
                '<h4>' + dish.name + '</h4>' +
                '<p>' + dish.desc + '</p>' +
            '</div>' +
            '<span class="option-price" style="color:var(--success);">Included</span>' +
        '</div>';
        if (dish.hasSpice) hasSpiceOption = true;
    });
    
    if (dishOptions) dishOptions.innerHTML = html;
    container.style.display = 'block';
    if (spiceGroup) spiceGroup.style.display = hasSpiceOption ? 'block' : 'none';
}

function toggleProteinDish(card) {
    const input = card.querySelector('input');
    input.checked = !input.checked;
    card.classList.toggle('selected', input.checked);
    updateCalculator();
}

function selectSpice(btn) {
    document.querySelectorAll('#spiceLevel .spice-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    const input = document.querySelector('input[name="spice_level"]');
    if (input) input.value = btn.dataset.value;
}

/* ═══════════════ MULTI-PROTEIN SELECTION ═══════════════ */
function toggleMultiProtein(card, proteinType) {
    const input = card.querySelector('input');
    input.checked = !input.checked;
    card.classList.toggle('selected', input.checked);
    
    if (input.checked) {
        window.selectedProteins.add(proteinType);
    } else {
        window.selectedProteins.delete(proteinType);
    }
    
    updateMultiProteinDishes();
}

function updateMultiProteinDishes() {
    const container = document.getElementById('multiProteinDishes');
    const spiceGroup = document.getElementById('multiSpiceLevelGroup');
    
    if (!container) return;
    
    if (window.selectedProteins.size === 0) {
        container.innerHTML = '';
        if (spiceGroup) spiceGroup.style.display = 'none';
        return;
    }
    
    let html = '';
    let hasSpiceOption = false;
    
    window.selectedProteins.forEach(function(protein) {
        const dishes = window.proteinDishes[protein] || [];
        html += '<div class="form-group" style="margin-top:20px;">' +
            '<label style="color:var(--gold);font-size:0.9rem;margin-bottom:12px;display:block;">' + protein + ' Dishes</label>' +
            '<div class="option-grid" id="multiDishes_' + protein.replace(/\s/g, '') + '">';
        
        dishes.forEach(function(dish) {
            html += '<div class="option-card" onclick="toggleMultiDish(this, \'' + protein + '\', \'' + dish.name + '\')">' +
                '<input type="checkbox" name="multi_dishes[' + protein + '][]" value="' + dish.name + '" data-has-spice="' + dish.hasSpice + '">' +
                '<span class="checkmark"></span>' +
                '<div class="option-info">' +
                    '<h4>' + dish.name + '</h4>' +
                    '<p>' + dish.desc + '</p>' +
                '</div>' +
                '<span class="option-price" style="color:var(--success);">Included</span>' +
            '</div>';
            if (dish.hasSpice) hasSpiceOption = true;
        });
        
        html += '</div></div>';
    });
    
    container.innerHTML = html;
    if (spiceGroup) spiceGroup.style.display = hasSpiceOption ? 'block' : 'none';
}

function toggleMultiDish(card, protein, dishName) {
    const input = card.querySelector('input');
    input.checked = !input.checked;
    card.classList.toggle('selected', input.checked);
    updateCalculator();
}

function selectMultiSpice(btn) {
    document.querySelectorAll('#multiSpiceLevel .spice-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    const input = document.querySelector('input[name="spice_level_multi"]');
    if (input) input.value = btn.dataset.value;
}

/* ═══════════════ TOGGLE OPTIONS ═══════════════ */
function toggleOption(card) {
    // Skip if card is unavailable
    if (card.dataset && card.dataset.unavailable === 'true') return;
    
    const input = card.querySelector('input');
    if (!input) return;
    
    if (input.type === 'checkbox') {
        input.checked = !input.checked;
        card.classList.toggle('selected', input.checked);
    } else if (input.type === 'radio') {
        const wasSelected = card.classList.contains('selected');
        const container = card.closest('.option-grid');
        
        if (container) {
            container.querySelectorAll('.option-card').forEach(function(c) {
                c.classList.remove('selected');
                const radioInput = c.querySelector('input');
                if (radioInput) radioInput.checked = false;
            });
        }
        
        if (!wasSelected) {
            card.classList.add('selected');
            input.checked = true;
        } else {
            card.classList.remove('selected');
            input.checked = false;
        }
    }
    updateCalculator();
}

/* ═══════════════ NO THANKS OPTION ═══════════════ */
function selectNoThanks(card) {
    const container = card.closest('.option-grid');
    if (!container) return;
    
    container.querySelectorAll('.option-card').forEach(function(c) {
        if (!c.dataset.noThanks) {
            c.classList.remove('selected');
            const input = c.querySelector('input[type="checkbox"]');
            if (input) input.checked = false;
        }
    });
    card.classList.add('selected');
    const input = card.querySelector('input');
    if (input) input.checked = true;
    updateCalculator();
}

/* ═══════════════ UNAVAILABLE SERVICE ═══════════════ */
let serviceMessageTimeout = null;

function showServiceMessage(card, message) {
    const msgBox = document.getElementById('serviceMessage');
    const msgText = document.getElementById('serviceMessageText');
    if (!msgBox || !msgText) return;
    
    msgText.textContent = message;
    msgBox.style.display = 'block';
    
    clearTimeout(serviceMessageTimeout);
    serviceMessageTimeout = setTimeout(function() {
        msgBox.style.display = 'none';
    }, 2000);
}

function handleUnavailableService(card, message) {
    showServiceMessage(card, message);
    return false;
}

/* ═══════════════ VALIDATION ═══════════════ */
function validateStep(step) {
    console.log('[Validation] Validating step:', step);
    const stepEl = document.getElementById('step' + step);
    if (!stepEl) {
        console.error('[Validation] Step element not found:', step);
        return false;
    }
    
    const required = stepEl.querySelectorAll('[required]');
    let valid = true;

    required.forEach(function(field) {
        // Skip hidden inputs and disabled fields
        if (field.type === 'hidden' || field.disabled) return;
        
        field.style.borderColor = '';
        if (!field.value.trim()) {
            field.style.borderColor = '#e74c3c';
            valid = false;
        }
        if (field.type === 'email' && field.value && !isValidEmail(field.value)) {
            field.style.borderColor = '#e74c3c';
            valid = false;
        }
    });

    // Step 1 validation
    if (step === 1) {
        const category = document.getElementById('eventCategory');
        const type = document.getElementById('eventType');
        const otherInput = document.getElementById('eventTypeOther');
        
        // Reset border colors
        if (category) category.style.borderColor = '';
        if (type) type.style.borderColor = '';
        if (otherInput) otherInput.style.borderColor = '';
        
        if (category && !category.value) {
            category.style.borderColor = '#e74c3c';
            valid = false;
        }
        
        if (type && !type.value) {
            type.style.borderColor = '#e74c3c';
            valid = false;
        }
        
        if (type && type.value === 'Other' && otherInput && !otherInput.value.trim()) {
            otherInput.style.borderColor = '#e74c3c';
            valid = false;
        }
        
        // Time validation (optional)
        const timeInput = document.getElementById('event_time');
        if (timeInput && timeInput.value && timeInput.value.trim() !== '') {
            const timePattern = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
            if (!timePattern.test(timeInput.value)) {
                alert('Please enter a valid time in 24-hour format (HH:MM)');
                valid = false;
            }
        }
    }

    // Step 2 validation
    if (step === 2) {
        if (window.currentDishType === 'single') {
            const protein = document.querySelector('input[name="protein_type"]:checked');
            if (!protein) {
                alert('Please select a protein type.');
                valid = false;
            }
        } else {
            const checkedProteins = document.querySelectorAll('input[name="protein_types[]"]:checked');
            if (checkedProteins.length === 0) {
                alert('Please select at least one protein type.');
                valid = false;
            }
        }
    }
    
    // Step 5 validation (location)
    if (step === 5) {
        const postcode = document.getElementById('autocompletePostcode');
        const address = document.getElementById('autocompleteAddress');
        
        if (postcode && !postcode.value.trim()) {
            postcode.style.borderColor = '#e74c3c';
            valid = false;
        }
        
        if (address && !address.value.trim()) {
            address.style.borderColor = '#e74c3c';
            valid = false;
        }
    }
    
    // Step 6 validation (summary - payment)
    if (step === 6) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            alert('Please select a payment method.');
            valid = false;
        }
    }
    
    console.log('[Validation] Result:', valid);
    return valid;
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/* ═══════════════ ALLERGY COUNT TOGGLE ═══════════════ */
function toggleAllergyCount(checkbox) {
    console.log('[Allergy] toggleAllergyCount called');
    const row = checkbox.closest('div');
    if (!row) return;
    
    const countInput = row.querySelector('input[type="number"]');
    if (!countInput) return;
    
    if (checkbox.checked) {
        countInput.disabled = false;
        countInput.style.opacity = '1';
        countInput.value = countInput.value || 1;
    } else {
        countInput.disabled = true;
        countInput.style.opacity = '0.5';
        countInput.value = 0;
    }
    
    // Trigger calculator update
    updateCalculator();
}

/* ═══════════════ PRICE CALCULATOR ═══════════════ */
/* ═══════════════ PRICE CALCULATOR (Forwarder) ═══════════════ */
// This function is now fully implemented in calculator.js for consolidation.
// Any calls to updateCalculator() will now use the one in calculator.js.
if (typeof updateCalculator === 'undefined') {
    window.updateCalculator = function() {
        console.warn('updateCalculator from calculator.js not loaded yet');
    };
}

/* ═══════════════ SUMMARY UPDATE ═══════════════ */
function updateSummary() {
    console.log('[Summary] updateSummary called');
    const form = document.getElementById('bookingForm');
    if (!form) return;
    
    const fd = new FormData(form);
    
    // ════ Event Details ════
    const category = fd.get('event_category') || '—';
    let type = fd.get('event_type') || '—';
    if (type === 'Other') {
        type = fd.get('event_type_other') || 'Other';
    }
    
    setSummaryText('summaryEvent', (category !== '—' ? category + ' - ' : '') + type);
    setSummaryText('summaryDate', (fd.get('event_date') || '—') + ' @ ' + (fd.get('event_time') || 'N/A'));
    setSummaryText('summaryVenue', (fd.get('address') || '—'));
    
    // ════ Guest Info (Handled by updateCalculator for breakdown) ════
    
    // ════ Menu Selection ════
    const dishType = fd.get('dish_type') || 'single';
    let menuHtml = '';
    
    // Collect all selected items across sections
    const sections = [
        { label: 'Rice', selector: '#riceGrid .option-card.selected h4' },
        { label: 'Bread', selector: '#breadGrid .option-card.selected h4' },
        { label: 'Salad', selector: '#saladGrid .option-card.selected h4' },
        { label: 'Sauce', selector: '#sauceGrid .option-card.selected h4' },
        { label: 'Desserts', selector: '#dessertsGrid .option-card.selected h4' },
        { label: 'Starters', selector: '#startersGrid .option-card.selected h4' },
        { label: 'Drinks', selector: '#drinksGrid .option-card.selected h4' },
        { label: 'Services', selector: '#servicesGrid .option-card.selected h4' }
    ];

    // Protein Logic
    let proteins = '';
    if (dishType === 'single') {
        proteins = fd.get('protein_type') || 'Not selected';
    } else {
        proteins = Array.from(document.querySelectorAll('input[name="protein_types[]"]:checked')).map(p => p.value).join(', ') || 'Not selected';
    }
    menuHtml += `<div style="margin-bottom:12px;"><strong style="color:var(--gold-dark);display:block;margin-bottom:2px;">Main Proteins (${dishType.toUpperCase()})</strong><span style="color:var(--charcoal);">${proteins}</span></div>`;

    // Collective List
    let itemsFound = false;
    sections.forEach(section => {
        const items = Array.from(document.querySelectorAll(section.selector)).map(el => el.textContent);
        if (items.length > 0) {
            itemsFound = true;
            menuHtml += `<div style="margin-bottom:12px;">
                            <strong style="color:var(--gold-dark);display:block;margin-bottom:2px;">${section.label}</strong>
                            <span style="color:var(--charcoal);">${items.join(', ')}</span>
                         </div>`;
        }
    });

    if (!itemsFound && !proteins) {
        menuHtml = '<p style="color:var(--mid-grey);font-style:italic;">No items selected yet</p>';
    }

    const menuEl = document.getElementById('summaryMenu');
    if (menuEl) menuEl.innerHTML = menuHtml;
}

function setSummaryText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

/* ═══════════════ POSTCODE LOOKUP WITH GOOGLE MAPS ═══════════════ */

// UK Postcode to Coordinates mapping (common postcodes)
const postcodeCoordinates = {
    'SW1A': { lat: 51.5034, lng: -0.1276 },
    'SW1': { lat: 51.5034, lng: -0.1276 },
    'W1': { lat: 51.5154, lng: -0.1419 },
    'W1D': { lat: 51.5154, lng: -0.1419 },
    'SW11': { lat: 51.4638, lng: -0.1680 },
    'M1': { lat: 53.4808, lng: -2.2426 },
    'B1': { lat: 52.4862, lng: -1.8904 },
    'LS1': { lat: 53.7965, lng: -1.5477 },
    'E1': { lat: 51.5099, lng: -0.0059 },
    'N1': { lat: 51.5315, lng: -0.1232 }
};

// Generate more addresses based on postcode prefix
function getAddressesForPostcode(postcode) {
    if (!postcode || postcode.length < 2) return [];
    
    const postcodeUpper = postcode.toUpperCase().trim();
    const prefix = postcodeUpper.replace(/\s/g, '').substring(0, 4);
    
    // Extended mock database
    const mockData = {
        'SW1A': [
            { number: '10', street: 'Downing Street', town: 'London' },
            { number: '11', street: 'Downing Street', town: 'London' },
            { number: '12', street: 'Downing Street', town: 'London' },
            { number: '1', street: 'Whitehall', town: 'London' }
        ],
        'SW1': [
            { number: '1', street: 'Parliament Square', town: 'London' },
            { number: '2', street: 'Bridge Street', town: 'London' },
            { number: '20', street: 'Great Smith Street', town: 'London' }
        ],
        'W1D': [
            { number: '123', street: 'Oxford Street', town: 'London' },
            { number: '125', street: 'Oxford Street', town: 'London' },
            { number: '127', street: 'Oxford Street', town: 'London' },
            { number: '100', street: 'Regent Street', town: 'London' }
        ],
        'W1': [
            { number: '1', street: 'Piccadilly', town: 'London' },
            { number: '50', street: 'Piccadilly', town: 'London' },
            { number: '25', street: 'Piccadilly', town: 'London' }
        ],
        'SW11': [
            { number: '1', street: 'Lavender Hill', town: 'London' },
            { number: '10', street: 'Lavender Hill', town: 'London' },
            { number: '25', street: 'Lavender Hill', town: 'London' },
            { number: '45', street: 'St Johns Road', town: 'London' }
        ],
        'M1': [
            { number: '1', street: 'Market Street', town: 'Manchester' },
            { number: '10', street: 'Market Street', town: 'Manchester' },
            { number: '100', street: 'Oxford Street', town: 'Manchester' }
        ],
        'B1': [
            { number: '1', street: 'New Street', town: 'Birmingham' },
            { number: '25', street: 'New Street', town: 'Birmingham' },
            { number: '50', street: 'Colmore Row', town: 'Birmingham' }
        ],
        'LS1': [
            { number: '1', street: 'Queen Street', town: 'Leeds' },
            { number: '10', street: 'Queen Street', town: 'Leeds' },
            { number: '25', street: 'Bond Street', town: 'Leeds' }
        ],
        'E1': [
            { number: '1', street: 'Commercial Street', town: 'London' },
            { number: '10', street: 'Commercial Street', town: 'London' },
            { number: '100', street: 'Whitechapel High Street', town: 'London' }
        ],
        'N1': [
            { number: '1', street: 'Upper Street', town: 'London' },
            { number: '10', street: 'Upper Street', town: 'London' },
            { number: '100', street: 'City Road', town: 'London' }
        ]
    };
    
    // Try exact match first
    for (const key in mockData) {
        if (prefix === key || prefix.startsWith(key) || key.startsWith(prefix.substring(0, 3))) {
            return mockData[key];
        }
    }
    
    // Generate generic addresses
    const streets = ['High Street', 'Station Road', 'Church Lane', 'Victoria Road', 'Manor Road', 'London Road'];
    const towns = ['London', 'Manchester', 'Birmingham', 'Leeds', 'Liverpool', 'Glasgow'];
    const seed = prefix.charCodeAt(0) + (prefix.charCodeAt(1) || 0);
    
    return [
        { number: '1', street: streets[seed % streets.length], town: towns[seed % towns.length] },
        { number: '2', street: streets[seed % streets.length], town: towns[seed % towns.length] },
        { number: '3', street: streets[seed % streets.length], town: towns[seed % towns.length] },
        { number: '5', street: streets[seed % streets.length], town: towns[seed % towns.length] },
        { number: '10', street: streets[seed % streets.length], town: towns[seed % towns.length] }
    ];
}

function handlePostcodeInput(postcode) {
    const resultsContainer = document.getElementById('postcodeResults');
    const houseSelect = document.getElementById('houseNumberSelect');
    const addressInput = document.getElementById('autocompleteAddress');
    
    if (!resultsContainer || !houseSelect) return;
    
    // Clear address if postcode is cleared
    if (postcode.length < 2) {
        resultsContainer.style.display = 'none';
        houseSelect.innerHTML = '<option value="">Select postcode first</option>';
        houseSelect.disabled = true;
        if (addressInput) addressInput.value = '';
        return;
    }
    
    const addresses = getAddressesForPostcode(postcode);
    
    if (addresses.length > 0) {
        let html = '';
        addresses.forEach(function(addr, index) {
            const fullAddress = addr.number + ' ' + addr.street + ', ' + addr.town;
            html += '<div onclick="selectPostcodeAddress(' + index + ', \'' + postcode.toUpperCase() + '\')" ' +
                'style="padding:12px 16px;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.08);transition:background 0.2s;" ' +
                'onmouseover="this.style.background=\'rgba(212,175,55,0.15)\'" ' +
                'onmouseout="this.style.background=\'transparent\'">' +
                '<div style="display:flex;align-items:center;gap:12px;">' +
                    '<i class="fas fa-home" style="color:var(--gold);"></i>' +
                    '<div>' +
                        '<div style="color:var(--white);font-weight:500;">' + addr.number + ' ' + addr.street + '</div>' +
                        '<div style="color:var(--mid-grey);font-size:0.85rem;">' + addr.town + ', ' + postcode.toUpperCase() + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        });
        
        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
        
        // Populate house number dropdown
        houseSelect.innerHTML = '<option value="">Select house number</option>';
        addresses.forEach(function(addr, index) {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = addr.number + ' ' + addr.street;
            houseSelect.appendChild(option);
        });
        houseSelect.disabled = false;
    } else {
        resultsContainer.innerHTML = '<div style="padding:16px;color:var(--mid-grey);">No addresses found. Try a different postcode or enter address manually.</div>';
        resultsContainer.style.display = 'block';
    }
}

function selectPostcodeAddress(index, postcode) {
    const addresses = getAddressesForPostcode(postcode);
    if (!addresses[index]) return;
    
    const addr = addresses[index];
    const fullAddress = addr.number + ' ' + addr.street + ', ' + addr.town;
    const fullAddressWithPostcode = fullAddress + ', ' + postcode.toUpperCase();
    
    const addressInput = document.getElementById('autocompleteAddress');
    const fullAddressInput = document.getElementById('fullAddressInput');
    const resultsContainer = document.getElementById('postcodeResults');
    const houseSelect = document.getElementById('houseNumberSelect');
    
    if (addressInput) addressInput.value = fullAddressWithPostcode;
    if (fullAddressInput) fullAddressInput.value = fullAddressWithPostcode;
    if (resultsContainer) resultsContainer.style.display = 'none';
    if (houseSelect) houseSelect.value = index;
    
    // Get coordinates and update map
    const coords = getCoordinatesForPostcode(postcode);
    updateMapWithCoords(fullAddressWithPostcode, coords.lat, coords.lng);
}

function lookupPostcode() {
    const postcode = document.getElementById('autocompletePostcode');
    if (postcode && postcode.value && postcode.value.length >= 3) {
        handlePostcodeInput(postcode.value);
    }
}

function hideAddressDropdownDelayed() {
    setTimeout(function() {
        const resultsContainer = document.getElementById('postcodeResults');
        if (resultsContainer) resultsContainer.style.display = 'none';
    }, 300);
}

// Get coordinates for postcode
function getCoordinatesForPostcode(postcode) {
    const postcodeUpper = postcode.toUpperCase().trim();
    const prefix = postcodeUpper.replace(/\s/g, '').substring(0, 4);
    
    // Try exact match
    for (const key in postcodeCoordinates) {
        if (prefix === key || prefix.startsWith(key)) {
            return postcodeCoordinates[key];
        }
    }
    
    // Default to central London if not found
    return { lat: 51.5074, lng: -0.1278 };
}

// Update map with coordinates using Google Maps Embed API
function updateMapWithCoords(address, lat, lng) {
    const mapContainer = document.getElementById('map');
    const latInput = document.getElementById('latitudeInput');
    const lngInput = document.getElementById('longitudeInput');
    
    if (!mapContainer) return;
    
    // Store coordinates
    if (latInput) latInput.value = lat;
    if (lngInput) lngInput.value = lng;
    
    // Use OpenStreetMap embed (free, no API key needed)
    const embedUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=' + 
        (lng - 0.01) + '%2C' + (lat - 0.01) + '%2C' + 
        (lng + 0.01) + '%2C' + (lat + 0.01) + 
        '&layer=mapnik&marker=' + lat + '%2C' + lng;
    
    mapContainer.innerHTML = '<iframe width="100%" height="100%" frameborder="0" style="border:0" ' +
        'src="' + embedUrl + '" ' +
        'allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
}

function updateMapFromAddress(address, postcode) {
    const coords = getCoordinatesForPostcode(postcode || '');
    updateMapWithCoords(address, coords.lat, coords.lng);
}

/* ═══════════════ PAYMENT METHOD ═══════════════ */
function selectPayment(card, method) {
    const grid = card.closest('.option-grid');
    if (!grid) return;
    
    grid.querySelectorAll('.option-card').forEach(function(c) {
        c.classList.remove('selected');
        const input = c.querySelector('input');
        if (input) input.checked = false;
    });
    
    card.classList.add('selected');
    const input = card.querySelector('input');
    if (input) input.checked = true;
}

/* ═══════════════ FORM SUBMISSION ═══════════════ */
function submitBooking(event) {
    if (event) event.preventDefault();
    
    if (!validateStep(6)) return false;
    
    const form = document.getElementById('bookingForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    }
    
    const formData = new FormData(form);
    
    // Add calc details
    const calc = getCalculation();
    const paymentTierInput = document.querySelector('input[name="payment_percent"]:checked');
    const paymentPercent = paymentTierInput ? parseInt(paymentTierInput.value) : 10;
    const amountPayableNow = calc.grandTotal * (paymentPercent / 100);

    formData.append('grand_total', calc.grandTotal);
    formData.append('amount_payable_now', amountPayableNow);
    formData.append('payment_percentage', paymentPercent);

    // 1. Create Booking & Get PaymentIntent
    fetch(ZAMAHI.siteUrl + '/api/process_payment.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Payment initiation failed');
        
        // 2. Handle Stripe Confirmation
        return window.stripe.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: window.cardElement,
                billing_details: {
                    name: formData.get('customer_name'),
                    email: formData.get('customer_email'),
                    phone: formData.get('customer_phone')
                }
            }
        }).then(result => {
            if (result.error) throw new Error(result.error.message);
            
            if (result.paymentIntent.status === 'succeeded') {
                // 3. Finalize Booking
                return fetch(ZAMAHI.siteUrl + '/api/payment_success.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        booking_id: data.booking_id,
                        payment_intent: result.paymentIntent.id
                    })
                }).then(res => res.json());
            } else {
                throw new Error('Payment status: ' + result.paymentIntent.status);
            }
        });
    })
    .then(data => {
        if (data.success) {
            showSuccessModal(data.ref_number);
        } else {
            throw new Error(data.message || 'Finalization failed');
        }
    })
    .catch(error => {
        console.error('[Stripe] Error:', error);
        alert('Payment Error: ' + error.message);
        resetSubmitButton();
    });
    
    return false;
}

// Stripe Loader
window.onload = function() {
    if (typeof Stripe !== 'undefined' && ZAMAHI.stripePublishableKey) {
        window.stripe = Stripe(ZAMAHI.stripePublishableKey);
        const elements = window.stripe.elements();
        
        const style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': { color: '#aab7c4' }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        window.cardElement = elements.create('card', { style: style });
        window.cardElement.mount('#card-element');

        window.cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
                displayError.style.display = 'block';
            } else {
                displayError.textContent = '';
                displayError.style.display = 'none';
            }
        });
    }
};

function resetSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
    }
}

function showSuccessModal(refNumber) {
    console.log('[Modal] Showing success modal with ref:', refNumber);
    
    const modal = document.getElementById('successModal');
    const refEl = document.getElementById('modalRef');
    
    if (refEl) {
        refEl.textContent = refNumber;
        console.log('[Modal] Set ref number:', refNumber);
    }
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        console.log('[Modal] Modal activated');
    } else {
        console.error('[Modal] Modal element not found');
        alert('Booking confirmed! Reference: ' + refNumber);
    }
}

function closeModal() {
    const modal = document.getElementById('successModal');
    if (modal) modal.classList.remove('active');
    document.body.style.overflow = '';
    window.location.reload();
}

/* ═══════════════ PROMO CODE ═══════════════ */
function applyPromo() {
    const code = document.querySelector('input[name="promo_code"]');
    if (!code || !code.value.trim()) return;
    
    alert('Promo code functionality will be available soon!');
}

/* ═══════════════ DOM INITIALIZATION ═══════════════ */
document.addEventListener('DOMContentLoaded', function() {
    console.log('[Init] DOM Content Loaded');
    
    // Initialize time picker
    initTimePicker('eventTimeContainer', 'event_time');
    
    // Initialize venue type listeners
    document.querySelectorAll('#venueType .spice-btn, #venueType2 .spice-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            selectVenueType(this);
        });
    });
    
    // House number select listener
    const houseSelect = document.getElementById('houseNumberSelect');
    if (houseSelect) {
        houseSelect.addEventListener('change', function() {
            const index = this.value;
            const postcode = document.getElementById('autocompletePostcode');
            if (index !== '' && postcode && postcode.value) {
                selectPostcodeAddress(parseInt(index), postcode.value.toUpperCase());
            }
        });
    }
    
    // Address input listener
    const postcodeInput = document.getElementById('autocompletePostcode');
    if (postcodeInput) {
        postcodeInput.addEventListener('input', function() {
            handlePostcodeInput(this.value);
        });
        postcodeInput.addEventListener('blur', hideAddressDropdownDelayed);
    }
    
    console.log('[Init] Initialization complete');
});

// ═══════════════════ MODERN LOCATION SYSTEM ═══════════════════

// Quick location search function
function quickLocationSearch(location) {
    const input = document.getElementById('autocompletePostcode');
    if (input) {
        input.value = location;
        lookupPostcode();
    }
}

// Enhanced location search with modern UI updates
function handlePostcodeInput(value) {
    const resultsDiv = document.getElementById('postcodeResults');
    if (!resultsDiv) return;

    if (value.length < 3) {
        resultsDiv.style.display = 'none';
        return;
    }

    // Show loading state
    resultsDiv.innerHTML = '<div style="padding:16px;text-align:center;color:var(--mid-grey);"><i class="fas fa-spinner fa-spin" style="margin-right:8px;"></i>Searching...</div>';
    resultsDiv.style.display = 'block';

    // Simulate API call with mock results
    setTimeout(() => {
        const mockResults = [
            { address: `${value.toUpperCase()} 1AA - Main Street, City Centre`, postcode: value.toUpperCase() },
            { address: `${value.toUpperCase()} 2BB - High Street, Downtown`, postcode: value.toUpperCase() },
            { address: `${value.toUpperCase()} 3CC - Park Road, Residential Area`, postcode: value.toUpperCase() }
        ];

        resultsDiv.innerHTML = mockResults.map((result, index) =>
            `<div onclick="selectLocationResult('${result.address}', '${result.postcode}')">${result.address}</div>`
        ).join('');
    }, 500);
}

// Select location from search results
function selectLocationResult(address, postcode) {
    const input = document.getElementById('autocompletePostcode');
    const resultsDiv = document.getElementById('postcodeResults');
    const detailsDiv = document.getElementById('locationDetails');

    if (input) input.value = postcode;
    if (resultsDiv) resultsDiv.style.display = 'none';

    // Update location details
    if (detailsDiv) {
        document.getElementById('selectedAddress').textContent = address;
        document.getElementById('locationPostcode').textContent = postcode;
        detailsDiv.style.display = 'block';
    }

    // Update map placeholder
    updateMapForLocation(address, postcode);
}

// Update map display for selected location
function updateMapForLocation(address, postcode) {
    const mapContainer = document.getElementById('map');
    const mapSubtitle = document.getElementById('mapSubtitle');

    if (mapSubtitle) {
        mapSubtitle.textContent = `Showing location: ${address}`;
    }

    if (mapContainer) {
        mapContainer.innerHTML = `
            <div class="map-active-view">
                <div class="map-active-content">
                    <i class="fas fa-map-marked-alt location-marker"></i>
                    <h4>Location Selected</h4>
                    <p class="selected-address-display">${address}</p>
                    <div class="map-actions">
                        <button class="map-action-btn" onclick="getDirections()">
                            <i class="fas fa-directions"></i> Get Directions
                        </button>
                        <button class="map-action-btn" onclick="viewFullMap()">
                            <i class="fas fa-expand"></i> Full Map
                        </button>
                    </div>
                </div>
                <div class="map-overlay-grid">
                    <div class="map-overlay-line horizontal"></div>
                    <div class="map-overlay-line horizontal"></div>
                    <div class="map-overlay-line horizontal"></div>
                    <div class="map-overlay-line vertical"></div>
                    <div class="map-overlay-line vertical"></div>
                    <div class="map-overlay-line vertical"></div>
                </div>
            </div>
        `;
    }
}

// Map action functions
function getDirections() {
    const address = document.getElementById('selectedAddress').textContent;
    const url = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(address)}`;
    window.open(url, '_blank');
}

function viewFullMap() {
    const address = document.getElementById('selectedAddress').textContent;
    const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
    window.open(url, '_blank');
}
