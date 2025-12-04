<footer class="border-t border-gray-200 bg-white pt-12 pb-8">
  <div class="mx-auto max-w-7xl px-6 lg:px-0">
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5">
      <!-- Products Column -->
      <div>
        <h3 class="text-sm font-semibold text-gray-900">Products</h3>
        <ul class="mt-6 space-y-4">
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">All products</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Features</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Security</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Team</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Enterprise</a>
          </li>
        </ul>
      </div>

      <!-- Resources Column -->
      <div>
        <h3 class="text-sm font-semibold text-gray-900">Resources</h3>
        <ul class="mt-6 space-y-4">
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Documentation</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">API Reference</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Guides</a>
          </li>
        </ul>
      </div>

      <!-- Company Column -->
      <div>
        <h3 class="text-sm font-semibold text-gray-900">Company</h3>
        <ul class="mt-6 space-y-4">
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">About</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Blog</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Careers</a>
          </li>
        </ul>
      </div>

      <!-- Support Column -->
      <div>
        <h3 class="text-sm font-semibold text-gray-900">Support</h3>
        <ul class="mt-6 space-y-4">
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Help Center</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Community</a>
          </li>
          <li>
            <a href="https://status.peopleos.cloud/" target="_blank" class="flex items-center gap-x-2 text-sm text-gray-600 hover:text-gray-900" aria-label="View system status">
              <span class="relative flex h-2 w-2" aria-hidden="true">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-500 opacity-75 duration-1000"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
              </span>
              Status
            </a>
          </li>
        </ul>
      </div>

      <!-- Social Column -->
      <div>
        <h3 class="text-sm font-semibold text-gray-900">Social</h3>
        <ul class="mt-6 space-y-4">
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">GitHub</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Twitter</a>
          </li>
          <li>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">LinkedIn</a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Bottom section -->
  <div class="mt-12 border-t border-gray-900/10 pt-8">
    <div class="mx-auto max-w-7xl px-6 lg:px-0">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-y-2">
          <div class="flex flex-col items-center gap-x-4 sm:flex-row sm:gap-x-2">
            <a href="{{ route('marketing.index') }}" class="group flex items-center gap-x-2 transition-transform ease-in-out">
              <div class="transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
                <x-logo width="25" height="25" />
              </div>
            </a>
            <p class="text-xs text-gray-600">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. Actually, our trademark is not registered, but we probably should write that to do like the big boys.</p>
          </div>

          <p class="text-xs text-gray-600">
            This site is inspired by the magnificent and funny
            <x-link href="https://posthog.com" target="_blank">Posthog website</x-link>
            .
          </p>
        </div>

        <div class="mt-6 sm:mt-0">
          <div class="mb-2 flex gap-x-4">
            <a href="" class="text-sm text-gray-600 hover:text-gray-900">Privacy</a>
            <a href="" class="text-sm text-gray-600 hover:text-gray-900">Terms</a>
          </div>

          <p class="flex gap-x-2 text-xs text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="48" height="48" viewBox="0 0 9600 4800">
              <title>Flag of Canada</title>
              <path fill="#f00" d="m0 0h2400l99 99h4602l99-99h2400v4800h-2400l-99-99h-4602l-99 99H0z" />
              <path fill="#fff" d="m2400 0h4800v4800h-4800zm2490 4430-45-863a95 95 0 0 1 111-98l859 151-116-320a65 65 0 0 1 20-73l941-762-212-99a65 65 0 0 1-34-79l186-572-542 115a65 65 0 0 1-73-38l-105-247-423 454a65 65 0 0 1-111-57l204-1052-327 189a65 65 0 0 1-91-27l-332-652-332 652a65 65 0 0 1-91 27l-327-189 204 1052a65 65 0 0 1-111 57l-423-454-105 247a65 65 0 0 1-73 38l-542-115 186 572a65 65 0 0 1-34 79l-212 99 941 762a65 65 0 0 1 20 73l-116 320 859-151a95 95 0 0 1 111 98l-45 863z" />
            </svg>
            Proudly Canadian
          </p>
        </div>
      </div>
    </div>
  </div>
</footer>
