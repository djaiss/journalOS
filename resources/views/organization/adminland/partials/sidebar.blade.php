<div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
  <ul class="space-y-1">
    <li class="group">
      <a href="{{ route('profile.edit') }}" class="group-hover:bg-slate-100 flex items-center px-2 py-1 rounded-lg {{ request()->routeIs('profile.*') ? 'bg-slate-100' : '' }}">
        <span><x-heroicon-s-cog class="w-4 h-4 mr-2 text-gray-500 group-hover:text-gray-800" /></span>
        <span>{{ __('General') }}</span>
      </a>
    </li>
    <li class="group">
      <a href="{{ $data['url']['sidebar_menu'] }}" class="group-hover:bg-slate-100 flex items-center px-2 py-1 rounded-lg {{ request()->routeIs('adminland.permission.index') ? 'bg-slate-100' : '' }}">
        <span><x-heroicon-s-key class="w-4 h-4 mr-2 text-gray-500 group-hover:text-gray-800" /></span>
        <span>{{ __('Permissions') }}</span>
      </a>
    </li>
  </ul>
</div>
