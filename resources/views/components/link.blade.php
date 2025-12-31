@props([
  'turbo' => true,
])

<a @if ($turbo) data-turbo="true" @endif {{
  $attributes->class([
    'inline underline',
    'underline-offset-4',
    'hover:decoration-[1.15px]',
    'decoration-gray-300',
    'hover:text-blue-600 hover:decoration-blue-400',
    'transition-colors duration-200',
    'dark:text-gray-300',
    'dark:decoration-gray-600',
    'dark:hover:text-blue-400 dark:hover:decoration-blue-600',
  ])
}}>{{ $slot }}</a>
