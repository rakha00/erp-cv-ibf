<x-filament-widgets::widget>
	<x-filament::section>
		<x-slot name="heading">
			Total Keuntungan Kantor & Total Gaji Karyawan Overview
		</x-slot>

		<form wire:submit.prevent="updateStats" class="p-4">
			{{ $this->form }}
		</form>

		<div class="grid grid-cols-2 gap-4 mt-4">
			@foreach ($stats as $stat)
				<div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
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