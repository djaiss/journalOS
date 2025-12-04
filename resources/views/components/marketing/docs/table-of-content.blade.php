<div class="mb-8 rounded-lg border border-gray-200 p-4">
  <p class="mb-2 text-xs">Table of contents</p>

  <ul>
    @foreach ($items as $item)
      <li>
        <x-link href="#{{ $item['id'] }}" :navigate="false">{{ $item['title'] }}</x-link>
      </li>
    @endforeach
  </ul>
</div>
