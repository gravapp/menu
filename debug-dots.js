// Debug script for dots functionality
// Run this in your browser console on your WordPress site

console.log('=== DEBUGGING DOTS FUNCTIONALITY ===');

// Check if jQuery is available
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded!');
} else {
    console.log('jQuery is available');
}

// Find the bottom nav wrapper
const $bottomNavWrapper = jQuery('.bnav_bottom_nav_wrapper');
console.log('Bottom nav wrapper found:', $bottomNavWrapper.length);

if ($bottomNavWrapper.length > 0) {
    // Check data attributes
    console.log('Data attributes:');
    console.log('- scrollbar-dots:', $bottomNavWrapper.data('scrollbar-dots'));
    console.log('- scrollbar-threshold:', $bottomNavWrapper.data('scrollbar-threshold'));
    console.log('- scrollbar-indicators:', $bottomNavWrapper.data('scrollbar-indicators'));
    
    // Check menu structure
    const $menuList = $bottomNavWrapper.find('ul.bnav_main_menu');
    console.log('Menu list found:', $menuList.length);
    
    if ($menuList.length > 0) {
        console.log('Menu details:');
        console.log('- Menu items:', $menuList.find('li').length);
        console.log('- Container width:', $menuList.width());
        console.log('- Total width:', $menuList[0].scrollWidth);
        console.log('- Overflow-x:', $menuList.css('overflow-x'));
        console.log('- Has scrollable class:', $bottomNavWrapper.hasClass('scrollable'));
        
        // Check if dots container exists
        const $dotsContainer = $bottomNavWrapper.find('.scroll-dots-container');
        console.log('Dots container exists:', $dotsContainer.length);
        
        // Force create dots for testing
        if ($dotsContainer.length === 0) {
            console.log('Creating test dots...');
            const $newDotsContainer = jQuery('<div class="scroll-dots-container"></div>');
            for (let i = 0; i < 3; i++) {
                const $dot = jQuery('<button class="scroll-dot" data-index="' + i + '"></button>');
                $newDotsContainer.append($dot);
            }
            $bottomNavWrapper.append($newDotsContainer);
            console.log('Test dots created!');
        }
    }
} else {
    console.error('Bottom nav wrapper not found!');
}

console.log('=== END DEBUG ==='); 