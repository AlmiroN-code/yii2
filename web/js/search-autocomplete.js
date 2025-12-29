/**
 * Search Autocomplete
 * Requirements: 7.1, 7.3, 7.4
 */
(function() {
    const searchInput = document.querySelector('.search-autocomplete');
    if (!searchInput) return;

    let debounceTimer;
    let dropdown;

    // Create dropdown
    function createDropdown() {
        dropdown = document.createElement('div');
        dropdown.className = 'absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-md shadow-lg mt-1 z-50 hidden';
        searchInput.parentElement.style.position = 'relative';
        searchInput.parentElement.appendChild(dropdown);
    }

    createDropdown();

    // Debounced search
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            hideDropdown();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch('/api/search/autocomplete?q=' + encodeURIComponent(query))
                .then(r => r.json())
                .then(results => {
                    if (results.length === 0) {
                        showNoResults();
                    } else {
                        showResults(results);
                    }
                });
        }, 300);
    });

    // Show results
    function showResults(results) {
        dropdown.innerHTML = results.map(item => `
            <a href="${item.url}" class="block px-4 py-2 hover:bg-gray-100 border-b last:border-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-0.5 rounded ${getTypeClass(item.type)}">${getTypeLabel(item.type)}</span>
                    <span class="font-medium text-gray-900">${escapeHtml(item.title)}</span>
                </div>
                ${item.preview ? `<p class="text-sm text-gray-500 mt-1 truncate">${escapeHtml(item.preview)}</p>` : ''}
            </a>
        `).join('');
        dropdown.classList.remove('hidden');
    }

    function showNoResults() {
        dropdown.innerHTML = '<div class="px-4 py-3 text-gray-500 text-center">Ничего не найдено</div>';
        dropdown.classList.remove('hidden');
    }

    function hideDropdown() {
        dropdown.classList.add('hidden');
    }

    function getTypeClass(type) {
        const classes = {
            publication: 'bg-blue-100 text-blue-800',
            category: 'bg-green-100 text-green-800',
            tag: 'bg-purple-100 text-purple-800'
        };
        return classes[type] || 'bg-gray-100 text-gray-800';
    }

    function getTypeLabel(type) {
        const labels = {
            publication: 'Статья',
            category: 'Категория',
            tag: 'Тег'
        };
        return labels[type] || type;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Hide on click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    // Enter to search
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                window.location.href = '/search?q=' + encodeURIComponent(query);
            }
        }
    });
})();
