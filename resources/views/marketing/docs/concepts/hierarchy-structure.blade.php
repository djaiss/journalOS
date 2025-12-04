<x-marketing-docs-layout>
  <div class="grid grid-cols-1 gap-x-16 lg:grid-cols-[1fr_250px]">
    <div class="py-16 sm:border-r sm:border-gray-200 sm:pr-10">
      <x-marketing.docs.h1 title="Hierarchical structure" />

      <p class="mb-2">{{ config('app.name') }} models real-world structure in a simple, flexible way. The goal is not to capture every HR detail, but to provide the essential building blocks:</p>
      <ul class="mb-2 list-disc pl-6">
        <li>Countries</li>
        <li>Offices</li>
        <li>Divisions</li>
        <li>Departments</li>
        <li>Teams</li>
        <li>Guilds</li>
      </ul>

      <p class="mb-8">
        To illustrate the concepts, we'll use examples from the fictional Dunder Mifflin Paper Company from
        <em>The Office</em>
        . The same structure works for remote-first companies and multi-country organizations.
      </p>

      <x-marketing.docs.h2 id="countries" title="Countries" />

      <p class="mb-2">Countries are the geographic foundation. They provide context for offices and employees and help with reporting and grouping.</p>

      <p class="mb-2">
        <strong>Example:</strong>
        Dunder Mifflin operates in the United States, and in our illustrative global variant, also has a presence in India.
      </p>

      <p class="mb-8">
        <strong>Remote-first note:</strong>
        Fully remote organizations may still define a “home country” for legal or tax reasons, or skip countries if not relevant.
      </p>

      <x-marketing.docs.h2 id="offices" title="Offices" />

      <p class="mb-2">Offices represent physical or virtual hubs where people work. They can be headquarters, branches, satellites, or a single "Remote (global)" placeholder for remote-only setups.</p>

      <p class="mb-10">
        <strong>Examples:</strong>
        Dunder Mifflin’s corporate HQ is in New York. The Scranton location is a branch office. A remote-first startup could designate a single office named Remote (global) and mark it as HQ if appropriate.
      </p>

      <x-marketing.docs.h2 id="divisions" title="Divisions" />

      <p class="mb-2">Divisions are the highest-level business areas, such as Sales, Operations, or Corporate Management. They give broad direction and accountability.</p>

      <p class="mb-10">
        <strong>Example:</strong>
        Corporate-level management (e.g., Jan Levinson for Sales, David Wallace for Finance/Corporate) sits at the division layer.
      </p>

      <x-marketing.docs.h2 id="departments" title="Departments" />

      <p class="mb-2">Departments are functional groupings inside divisions (e.g., Sales Department, Accounting, Customer Service). They are the backbone of day-to-day work.</p>

      <p class="mb-2">
        <strong>Nesting:</strong>
        Departments can contain
        <strong>sub-departments</strong>
        (e.g., Accounting → Payroll). This allows larger organizations to map real hierarchies while smaller companies can keep things flat.
      </p>

      <p class="mb-10">
        <strong>Examples:</strong>
        In Scranton, the Accounting Department (Angela, Oscar, Kevin) and Customer Service (Kelly) are departments under different divisions.
      </p>

      <x-marketing.docs.h2 id="teams" title="Teams" />

      <p class="mb-2">Teams are the smallest operational units and live inside departments. They represent the working group people identify with day-to-day.</p>

      <p class="mb-10">
        <strong>Example:</strong>
        The Scranton Sales Team includes Jim, Dwight, Phyllis, and Stanley, with Michael as the office’s manager.
      </p>

      <x-marketing.docs.h2 id="guilds" title="Guilds" />

      <p class="mb-2">Guilds are cross-functional communities of practice that cut across departments and divisions. They’re great for knowledge sharing and standards.</p>

      <p class="mb-10">
        <strong>Examples:</strong>
        The Party Planning Committee (Angela, Pam, Phyllis) and the Safety Committee (Dwight, Toby, Michael) are guilds that operate across the Scranton office; a Global Sales Guild can connect sales reps across countries.
      </p>

      <x-marketing.docs.h2 id="key-principles" title="Key principles" />

      <p class="mb-2">There are some basic rules to follow:</p>

      <ul class="mb-10 list-disc pl-6">
        <li>Every unit should have a clear name, a parent (except at the top), and a designated leader/owner.</li>
        <li>Employees primarily belong to teams, and can optionally join guilds for cross-functional collaboration.</li>
        <li>Offices are linked to countries, and any organizational unit can optionally be tied to an office when location matters.</li>
        <li>An organization can be entirely remote. In that case, you can replace physical offices with a single "Remote (global)" office and still designate it as HQ if appropriate.</li>
        <li>Departments can contain sub-departments. Keep things flat if you’re small; nest when you need the flexibility.</li>
      </ul>

      <x-marketing.docs.h2 id="headquarters-vs-branch-offices" title="Headquarters vs. branch offices" />

      <p class="mb-2">Headquarters (HQ) is the primary office of the organization. It typically houses executive leadership and is tied to legal registration, tax, and compliance. Your data model should allow exactly one office per organization to be flagged as HQ.</p>

      <p class="mb-2">Branch/Satellite offices are regular offices where divisions, departments, and teams operate locally. They can have their own leaders, but they report up through the organization to HQ.</p>

      <p class="mb-2">
        <strong>Remote-first organizations</strong>
        can set a single “Remote (Global)” office and mark it as HQ, or designate a small physical office as HQ while most employees work remotely.
      </p>
    </div>

    <!-- Sidebar -->
    <div class="mb-10 hidden w-full flex-shrink-0 flex-col justify-self-end lg:flex">
      <div class="bg-light dark:bg-dark z-10 pt-16">
        <div class="mb-1 flex items-center justify-between">
          <p class="text-xs">Written by...</p>
        </div>
        <div class="-mx-4 pt-1">
          <a href="" class="border-light dark:border-dark text-primary dark:text-primary-dark hover:text-primary dark:hover:text-primary-dark relative flex justify-between rounded border hover:border-b-[4px] hover:transition-all active:top-[2px] active:border-b-1 md:mx-4">
            <div class="flex w-full flex-col justify-between gap-0.5 px-4 py-2">
              <h3 class="mb-0 text-base leading-tight"><span>Régis Freyd</span></h3>
              <p class="text-primary/50 dark:text-primary-dark/50 m-0 line-clamp-1 text-sm leading-tight">Project maintainer</p>
            </div>
            <div class="flex-shrink-0 px-4 py-2">
              <x-image src="{{ asset('marketing/regis.webp') }}" srcset="{{ asset('marketing/regis.webp') }}, {{ asset('marketing/regis@2x.webp') }} 2x" alt="Regis" width="48" height="48" class="h-12 w-12 rounded-full" />
            </div>
          </a>
        </div>
      </div>
      <div class="flex flex-grow items-end">
        <div class="sticky bottom-0 w-full">
          <!-- Table of Contents -->
          <div class="mb-4">
            <h4 class="mb-2 text-xs font-semibold text-gray-500 uppercase">Jump to</h4>
            <nav class="space-y-1 text-sm">
              <a href="#countries" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Countries</a>
              <a href="#offices" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Offices</a>
              <a href="#divisions" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Divisions</a>
              <a href="#departments" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Departments</a>
              <a href="#teams" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Teams</a>
              <a href="#guilds" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Guilds</a>
              <a href="#key-principles" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Key principles</a>
              <a href="#headquarters-vs-branch-offices" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Headquarters vs. branch offices</a>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
