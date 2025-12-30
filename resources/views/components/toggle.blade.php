@props([
  'name',
  'checked' => false,
])

<div x-data="{ switchOn: @js($checked) }" class="flex items-center justify-center space-x-2">
  <input :id="$id('{{ $name }}')" type="hidden" name="{{ $name }}" :value="switchOn ? '1' : '0'" />
  <input type="checkbox" class="hidden" :checked="switchOn" />

  <button x-ref="switchButton" type="button" @click="switchOn = ! switchOn; $el.closest('form').requestSubmit()" :class="switchOn ? 'bg-blue-600' : 'bg-neutral-200'" class="relative ml-4 inline-flex h-6 w-10 rounded-full py-0.5 focus:outline-none" x-cloak>
    <span :class="switchOn ? 'translate-x-[18px]' : 'translate-x-0.5'" class="h-5 w-5 rounded-full bg-white shadow-md duration-200 ease-in-out"></span>
  </button>

  <label @click="$refs.switchButton.click(); $refs.switchButton.focus()" :for="$id('{{ $name }}')" :class="{ 'text-blue-600': switchOn, 'text-gray-400': ! switchOn }" class="text-sm select-none" x-cloak>
    {{ $slot }}
  </label>
</div>
