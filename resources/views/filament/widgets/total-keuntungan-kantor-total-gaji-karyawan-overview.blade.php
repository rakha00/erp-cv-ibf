<x-filament-widgets::widget>
	<x-filament::section>

		<div style="position: relative;">
			<form wire:submit.prevent="updateStats"
				style="position: absolute; right: 16px; display: flex; gap: 8px; align-items: center;">
				<div style="display: flex; flex-wrap: nowrap; gap: 8px;">
					<div style="width: 100px; font-size: 14px;">
						{{ $this->form->getComponent('year') }}
					</div>
					<div style="width: 100px; font-size: 14px;">
						{{ $this->form->getComponent('month') }}
					</div>
				</div>
			</form>
		</div>

		<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 80px;">
			@foreach ($stats as $stat)
				<div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800" style="width: 100%;">
					<div class="flex items-center text-gray-500 dark:text-gray-400">
						@if ($stat->getIcon())
							<x-filament::icon icon="{{$stat->getIcon()}}" class="w-5 h-5" style="margin-right: 8px;" />
						@endif
						<p class="text-sm font-medium uppercase">{{ $stat->getLabel() }}</p>
					</div>
					<p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stat->getValue() }}</p>
					@if ($stat->getDescription())
						<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stat->getDescription() }}</p>
					@endif
				</div>
			@endforeach
		</div>
	</x-filament::section>
</x-filament-widgets::widget>