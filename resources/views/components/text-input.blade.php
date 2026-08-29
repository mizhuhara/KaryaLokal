@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 rounded-xl border bg-white text-gray-800 text-sm transition-all duration-200 focus:outline-none focus:ring-2 placeholder-gray-400']) }}
       style="border-color: #F0E8E0;"
       onfocus="this.style.borderColor='#E8531D'; this.style.boxShadow='0 0 0 3px rgba(232, 83, 29, 0.12)';"
       onblur="this.style.borderColor='#F0E8E0'; this.style.boxShadow='none';">
