@push('scripts')
    <script src="/vendor/chartjs/chart.js"></script>
    <script>
        (function () {
            const BERANDA_COLORS = ['#FE6B78', '#F73C4C', '#E51D2E', '#C01624', '#9F1521'];
            const GRID_COLOR = '#e7e9ee';
            const TICK_COLOR = '#6b7280';
            const TOOLTIP_STYLE = {
                backgroundColor: '#ffffff',
                borderColor: '#e7e9ee',
                borderWidth: 1,
                cornerRadius: 6,
                titleColor: '#1f2937',
                titleFont: { size: 12, weight: '500' },
                bodyColor: '#1f2937',
                bodyFont: { size: 12 },
                padding: 8,
                displayColors: false,
            };

            function buildConfig(labels, values, chartType) {
                if (chartType === 'pie') {
                    return {
                        type: 'pie',
                        data: {
                            labels,
                            datasets: [{
                                data: values,
                                backgroundColor: labels.map((_, index) => BERANDA_COLORS[index % BERANDA_COLORS.length]),
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: TICK_COLOR, font: { size: 12 }, boxWidth: 10, boxHeight: 10, padding: 12 },
                                },
                                tooltip: TOOLTIP_STYLE,
                            },
                        },
                    };
                }

                const isLine = chartType === 'line';

                return {
                    type: isLine ? 'line' : 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Nilai',
                            data: values,
                            backgroundColor: isLine
                                ? BERANDA_COLORS[0]
                                : labels.map((_, index) => BERANDA_COLORS[index % BERANDA_COLORS.length]),
                            borderColor: BERANDA_COLORS[0],
                            borderWidth: isLine ? 2 : 0,
                            borderRadius: isLine ? 0 : { topLeft: 4, topRight: 4, bottomLeft: 0, bottomRight: 0 },
                            pointRadius: isLine ? 0 : 0,
                            pointHoverRadius: isLine ? 4 : 0,
                            tension: isLine ? 0.4 : 0,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { color: TICK_COLOR, font: { size: 12 }, autoSkip: false },
                            },
                            y: {
                                grid: { color: GRID_COLOR },
                                border: { display: false },
                                ticks: { color: TICK_COLOR, font: { size: 12 } },
                            },
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: TOOLTIP_STYLE,
                        },
                    },
                };
            }

            window.renderChartPreview = function (canvas, labels, values, chartType) {
                const existing = window.Chart && Chart.getChart(canvas);
                if (existing) existing.destroy();
                canvas.replaceChildren();
                if (!labels.length || !values.length) {
                    const empty = document.createElement('p');
                    empty.className = 'ds-preview__empty';
                    empty.textContent = 'Belum ada data untuk ditampilkan';
                    canvas.append(empty);
                    return;
                }
                new Chart(canvas, buildConfig(labels, values, chartType));
            };
        })();
    </script>
@endpush