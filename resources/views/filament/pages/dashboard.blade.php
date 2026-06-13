<x-filament-panels::page class="si-dashboard-page">
    <section class="si-dashboard-hero">
        <div>
            <p class="si-dashboard-eyebrow">Sistem Informasi Lost & Found</p>
            <h2>Dashboard</h2>
            <p>Pantau laporan kehilangan, laporan temuan, klaim barang, dan aktivitas harian dari satu layar.</p>
        </div>
    </section>

    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
        class="si-dashboard-widgets"
    />
</x-filament-panels::page>
