<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm text-white transition-all duration-200 hover:-translate-y-0.5']) }}
        style="background: #E8531D; box-shadow: 0 2px 8px rgba(232, 83, 29, 0.3);"
        onmouseover="this.style.background='#C4401A'; this.style.boxShadow='0 6px 20px rgba(232, 83, 29, 0.4)';"
        onmouseout="this.style.background='#E8531D'; this.style.boxShadow='0 2px 8px rgba(232, 83, 29, 0.3)';">
    {{ $slot }}
</button>
