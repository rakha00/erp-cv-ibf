<x-filament-widgets::widget>
	<x-filament::section>
		<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
			<h3 class="text-xl md:text-3xl font-semibold text-gray-900 dark:text-white mb-2 md:mb-0">Financial Overview
			</h3>
			<form wire:submit.prevent="updateStats"
				class="w-full md:w-auto flex flex-col md:flex-row gap-2 items-center">
				<div class="flex flex-col md:flex-row md:flex-wrap gap-2 w-full md:w-auto items-center">
					<div class="w-full md:w-auto">
						{{ $this->form->getComponent('year') }}
					</div>
					<div class="w-full md:w-auto">
						{{ $this->form->getComponent('month') }}
					</div>
					<div class="w-full md:w-auto">
						{{ $this->form->getComponent('salaryOverviewType') }}
					</div>
				</div>
			</form>
		</div>

		<div class="grid grid-cols-1 gap-4 mt-5 md:grid-cols-2">
			@foreach ($stats as $stat)
				<div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
					<div class="flex items-center text-gray-500 dark:text-gray-400">
						@if ($stat->getIcon())
							<x-filament::icon icon="{{$stat->getIcon()}}" class="w-5 h-5" style="margin-right: 8px;" />
						@endif
						<p class="text-sm font-medium uppercase">{{ $stat->getLabel() }}</p>
					</div>
					<p class="text-xl md:text-3xl font-semibold text-gray-900 dark:text-white mt-2">{{ $stat->getValue() }}
					</p>
					@if ($stat->getDescription())
						<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stat->getDescription() }}</p>
					@endif
				</div>
			@endforeach
		</div>
	</x-filament::section>
</x-filament-widgets::widget>