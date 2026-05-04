function updateCalculator() {
    const calc = getCalculation();

    // Per head
    const pricePerHeadEl = document.getElementById('pricePerHead');
    if (pricePerHeadEl) pricePerHeadEl.textContent = formatPrice(calc.perHead);

    // Billable guests
    const priceBillableEl = document.getElementById('priceBillable');
    if (priceBillableEl) priceBillableEl.textContent = calc.billableGuests;
    
    const priceSubtotalEl = document.getElementById('priceSubtotal');
    if (priceSubtotalEl) priceSubtotalEl.textContent = formatPrice(calc.subtotal);

    // Update Summary Elements (Breakdown)
    if (document.getElementById('summaryTotalGuests')) {
        document.getElementById('summaryTotalGuests').textContent = calc.totalGuests;
        document.getElementById('summaryChildren').textContent = calc.kidsCount;
        document.getElementById('summaryAdults').textContent = Math.max(0, calc.totalGuests - calc.kidsCount);
    }

    // Update Allergy Breakdown in Summary with Premium Styling
    const allergySummary = document.getElementById('summaryAllergyBreakdown');
    if (allergySummary) {
        let html = '';
        if (calc.allergyBreakdown.length > 0) {
            html = '<div style="margin-top:12px;padding-top:10px;border-top:1px dashed rgba(0,0,0,0.1);">';
            calc.allergyBreakdown.forEach(item => {
                html += `<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                            <span style="font-size:0.85rem;color:var(--mid-grey);display:flex;align-items:center;gap:6px;">
                                <i class="fas fa-exclamation-circle" style="color:var(--gold);font-size:0.7rem;"></i> ${item.name}
                            </span>
                            <span style="font-size:0.85rem;font-weight:600;color:var(--charcoal);">${item.count} Guests</span>
                         </div>`;
            });
            html += '</div>';
        }
        allergySummary.innerHTML = html;
    }

    // Discount
    const discountRow = document.getElementById('priceDiscountRow');
    if (discountRow) {
        if (calc.discount > 0) {
            discountRow.style.display = 'flex';
            document.getElementById('priceDiscount').textContent = '-' + formatPrice(calc.discount);
        } else {
            discountRow.style.display = 'none';
        }
    }

    // Allergy charges
    const allergyRow = document.getElementById('priceAllergyRow');
    if (allergyRow) {
        if (calc.allergyCharges > 0) {
            allergyRow.style.display = 'flex';
            document.getElementById('priceAllergy').textContent = formatPrice(calc.allergyCharges);
        } else {
            allergyRow.style.display = 'none';
        }
    }

    // Starters
    const startersRow = document.getElementById('priceStartersRow');
    if (startersRow) {
        if (calc.starterCharges > 0) {
            startersRow.style.display = 'flex';
            document.getElementById('priceStarters').textContent = formatPrice(calc.starterCharges);
        } else {
            startersRow.style.display = 'none';
        }
    }

    // Drinks
    const drinksRow = document.getElementById('priceDrinksRow');
    if (drinksRow) {
        if (calc.drinksCharge > 0) {
            drinksRow.style.display = 'flex';
            document.getElementById('priceDrinks').textContent = formatPrice(calc.drinksCharge);
        } else {
            drinksRow.style.display = 'none';
        }
    }

    // Services
    const servicesRow = document.getElementById('priceServicesRow');
    if (servicesRow) {
        if (calc.servicesTotal > 0) {
            servicesRow.style.display = 'flex';
            document.getElementById('priceServices').textContent = formatPrice(calc.servicesTotal);
        } else {
            servicesRow.style.display = 'none';
        }
    }

    // Delivery
    const deliveryRow = document.getElementById('priceDeliveryRow');
    if (deliveryRow) {
        const deliveryVal = document.getElementById('priceDelivery');
        if (deliveryVal) {
            deliveryVal.textContent = calc.deliveryCharge > 0 ? formatPrice(calc.deliveryCharge) : 'FREE';
            deliveryVal.style.color = calc.deliveryCharge === 0 ? '#2ecc71' : '';
        }
    }

    // VAT
    const priceVatEl = document.getElementById('priceVat');
    if (priceVatEl) priceVatEl.textContent = formatPrice(calc.vat);

    // Grand Total
    const priceTotalEl = document.getElementById('priceTotal');
    if (priceTotalEl) priceTotalEl.textContent = formatPrice(calc.grandTotal);

    // Payment Tier / Amount Payable Now
    const paymentTierInput = document.querySelector('input[name="payment_percent"]:checked');
    const paymentPercent = paymentTierInput ? parseInt(paymentTierInput.value) : 10;
    const amountPayable = calc.grandTotal * (paymentPercent / 100);
    const amountPayableEl = document.getElementById('amountPayableNow');
    if (amountPayableEl) {
        // Multi-stage animation effect for the price
        const currentVal = parseFloat(amountPayableEl.textContent.replace('£', '')) || 0;
        if (Math.abs(currentVal - amountPayable) > 0.01) {
            amountPayableEl.style.opacity = '0.5';
            setTimeout(() => {
                amountPayableEl.textContent = formatPrice(amountPayable);
                amountPayableEl.style.opacity = '1';
            }, 150);
        } else {
            amountPayableEl.textContent = formatPrice(amountPayable);
        }
    }

    // Step 4 Button Label Logic
    const serviceCbs = document.querySelectorAll('#servicesGrid input[name="services[]"]:checked');
    const nextBtn4 = document.querySelector('#step4 .btn-next');
    if (nextBtn4) {
        if (serviceCbs.length > 0) {
            nextBtn4.innerHTML = 'Next: Location <i class="fas fa-arrow-right"></i>';
        } else {
            nextBtn4.innerHTML = 'No Thanks <i class="fas fa-arrow-right"></i>';
        }
    }
}

function getCalculation() {
    const guestCount = parseInt(document.querySelector('input[name="guest_count"]')?.value) || 0;
    const kidsCount  = parseInt(document.querySelector('input[name="kids_count"]')?.value) || 0;
    
    // In this system, children (under 4) are free
    const billableGuests = Math.max(0, guestCount - kidsCount);

    const perHead = ZAMAHI.basePrice;
    const subtotal = billableGuests * perHead;

    // Guest discount
    let discount = 0;
    if (guestCount >= 50) {
        discount = subtotal * ZAMAHI.discount50;
    }

    // Allergy surcharges & breakdown
    let allergyCharges = 0;
    let allergyBreakdown = [];
    document.querySelectorAll('#allergySection input[type="checkbox"]:checked').forEach(cb => {
        const row = cb.closest('div');
        const countInput = row.querySelector('input[type="number"]');
        const count = parseInt(countInput?.value) || 0;
        if (count > 0) {
            allergyCharges += count * ZAMAHI.allergySurcharge;
            allergyBreakdown.push({
                name: cb.value,
                count: count
            });
        }
    });

    // Starter add-ons (price per head)
    let starterCharges = 0;
    document.querySelectorAll('#startersGrid input[type="checkbox"]:checked').forEach(cb => {
        starterCharges += (parseFloat(cb.dataset.price) || 0) * billableGuests;
    });

    // Drinks (price per head)
    let drinksCharge = 0;
    document.querySelectorAll('#drinksGrid input[type="checkbox"]:checked').forEach(cb => {
        drinksCharge += (parseFloat(cb.dataset.price) || 0) * billableGuests;
    });

    // Additional services (flat fee)
    let servicesTotal = 0;
    document.querySelectorAll('#servicesGrid input[type="checkbox"]:checked').forEach(cb => {
        servicesTotal += parseFloat(cb.dataset.price) || 0;
    });

    // Delivery
    let deliveryCharge = ZAMAHI.deliveryCharge;
    if (guestCount >= ZAMAHI.freeDeliveryThreshold) {
        deliveryCharge = 0;
    }

    // Free waiter for 150+ guests
    if (guestCount >= ZAMAHI.freeWaiterThreshold) {
        const waiterCb = document.querySelector('#servicesGrid input[value="waiter_hire"]:checked');
        if (waiterCb) {
            servicesTotal -= parseFloat(waiterCb.dataset.price) || 0;
        }
    }

    // Promo discount
    let promoDiscount = 0;
    if (window.promoDiscount) {
        if (window.promoDiscount.type === 'percentage') {
            promoDiscount = subtotal * (window.promoDiscount.value / 100);
        } else {
            promoDiscount = window.promoDiscount.value;
        }
    }

    const preVat = (subtotal - discount - promoDiscount) + allergyCharges + starterCharges + servicesTotal + deliveryCharge + drinksCharge;
    const vat = preVat * ZAMAHI.vatRate;
    const grandTotal = preVat + vat;

    return {
        totalGuests: guestCount,
        kidsCount: kidsCount,
        allergyBreakdown: allergyBreakdown,
        perHead: perHead,
        billableGuests: billableGuests,
        subtotal: round2(subtotal),
        discount: round2(discount + promoDiscount),
        allergyCharges: round2(allergyCharges),
        starterCharges: round2(starterCharges),
        drinksCharge: round2(drinksCharge),
        servicesTotal: round2(Math.max(0, servicesTotal)),
        deliveryCharge: round2(deliveryCharge),
        vat: round2(vat),
        grandTotal: round2(grandTotal)
    };
}

function formatPrice(amount) {
    return '£' + amount.toFixed(2);
}

function round2(num) {
    return Math.round(num * 100) / 100;
}

// Global selection handler for payment tiers
window.selectPaymentTier = function(card, percent) {
    const grid = card.closest('.option-grid');
    grid.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input').checked = true;
    updateCalculator();
};

// Run initial calculation on load
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(updateCalculator, 500);
});
