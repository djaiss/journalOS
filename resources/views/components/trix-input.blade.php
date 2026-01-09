@props([
  'id',
  'name',
  'value' => '',
])

<input type="hidden" name="{{ $name }}" id="{{ $id }}_input" value="{{ $value }}" />

<trix-toolbar class="[&_.trix-button]:bg-white [&_.trix-button.trix-active]:bg-gray-300" id="{{ $id }}_toolbar">
  <div class="trix-button-row">
    <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">
      <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="Bold" tabindex="-1">Bold</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="Italic" tabindex="-1">Italic</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-strike" data-trix-attribute="strike" title="Strikethrough" tabindex="-1">Strikethrough</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-link" data-trix-attribute="href" data-trix-action="link" data-trix-key="k" title="Link" tabindex="-1">Link</button>
    </span>

    <span class="trix-button-group trix-button-group--block-tools" data-trix-button-group="block-tools">
      <button type="button" class="trix-button trix-button--icon trix-button--icon-heading-1" data-trix-attribute="heading1" title="Heading" tabindex="-1">Heading</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-quote" data-trix-attribute="quote" title="Quote" tabindex="-1">Quote</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-code" data-trix-attribute="code" title="Code" tabindex="-1">Code</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-bullet-list" data-trix-attribute="bullet" title="Bullets" tabindex="-1">Bullets</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-number-list" data-trix-attribute="number" title="Numbers" tabindex="-1">Numbers</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-decrease-nesting-level" data-trix-action="decreaseNestingLevel" title="Decrease Level" tabindex="-1">Decrease Level</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-increase-nesting-level" data-trix-action="increaseNestingLevel" title="Increase Level" tabindex="-1">Increase Level</button>
    </span>

    <span class="trix-button-group-spacer"></span>

    <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">
      <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="Undo" tabindex="-1">Undo</button>
      <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="Redo" tabindex="-1">Redo</button>
    </span>
  </div>

  <div class="trix-dialogs" data-trix-dialogs>
    <div class="trix-dialog trix-dialog--link" data-trix-dialog="href" data-trix-dialog-attribute="href">
      <div class="trix-dialog__link-fields">
        <input type="url" name="href" class="trix-input trix-input--dialog" placeholder="Enter a URL..." aria-label="URL" data-trix-validate-href required data-trix-input />
        <div class="trix-button-group">
          <input type="button" class="trix-button trix-button--dialog" value="Link" data-trix-method="setAttribute" />
          <input type="button" class="trix-button trix-button--dialog" value="Unlink" data-trix-method="removeAttribute" />
        </div>
      </div>
    </div>
  </div>
</trix-toolbar>

<trix-editor id="{{ $id }}" toolbar="{{ $id }}_toolbar" input="{{ $id }}_input" x-data="" x-on:trix-file-accept.prevent x-on:trix-attachment-add="$event.attachment.file && $event.attachment.remove()" {{ $attributes->merge(['class' => 'trix-content border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-1 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm dark:[&_pre]:!bg-gray-700 dark:[&_pre]:rounded dark:[&_pre]:!text-white min-h-full h-full']) }}></trix-editor>
