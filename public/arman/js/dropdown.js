// Custom Dropdown JavaScript
function toggleDropdown() {
    const dropdown = document.querySelector('.custom-dropdown');
    const isActive = dropdown.classList.contains('active');

    // Close all other dropdowns first
    document.querySelectorAll('.custom-dropdown').forEach(dd => {
        dd.classList.remove('active');
    });

    // Toggle current dropdown
    if (!isActive) {
        dropdown.classList.add('active');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const dropdown = document.querySelector('.custom-dropdown');
    if (!dropdown) return;

    const isClickInside = dropdown.contains(event.target);

    if (!isClickInside && dropdown.classList.contains('active')) {
        dropdown.classList.remove('active');
    }
});

// Close dropdown when pressing Escape key
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        const dropdown = document.querySelector('.custom-dropdown');
        if (dropdown && dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
        }
    }
});

// Prevent dropdown from closing when clicking on dropdown content
document.addEventListener('DOMContentLoaded', function () {
    const dropdownContent = document.querySelector('.dropdown-content');
    if (dropdownContent) {
        dropdownContent.addEventListener('click', function (event) {
            // Only prevent if not clicking on a link or button
            if (!event.target.closest('.dropdown-link')) {
                event.stopPropagation();
            }
        });
    }
});

// Alternative: jQuery version (jika masih menggunakan jQuery)
$(document).ready(function () {
    // Toggle dropdown
    window.toggleDropdown = function () {
        const $dropdown = $('.custom-dropdown');
        const isActive = $dropdown.hasClass('active');

        // Close all dropdowns
        $('.custom-dropdown').removeClass('active');

        // Toggle current dropdown
        if (!isActive) {
            $dropdown.addClass('active');
        }
    };

    // Close dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.custom-dropdown').length) {
            $('.custom-dropdown').removeClass('active');
        }
    });

    // Close dropdown with Escape key
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('.custom-dropdown').removeClass('active');
        }
    });

    // Prevent dropdown from closing when clicking inside content
    $('.dropdown-content').on('click', function (e) {
        if (!$(e.target).closest('.dropdown-link').length) {
            e.stopPropagation();
        }
    });
});
