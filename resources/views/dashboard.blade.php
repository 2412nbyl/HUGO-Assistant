@extends('layout')

@section('page-title', 'Main Dashboard')

@section('content')
    <style>
        /* Darker muted text globally for white backgrounds */
        :root {
            --text-dark: #1f2937;
            --text-mid: #374151;
            --text-muted: #6b7280;
        }

        /* Hero */
        .hero-section {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            height: 220px;
            margin-bottom: 28px;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background: url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&q=80') center/cover no-repeat;
            opacity: .38;
        }

        .hero-fade {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 25%, #111827f0 100%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 32px 36px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .hero-content h2 {
            font-size: 22px;
            font-weight: 700;
            color: #f9fafb;
            margin-bottom: 6px;
        }

        .hero-content p {
            font-size: 13px;
            color: #d1d5db;
            max-width: 520px;
            line-height: 1.65;
        }

        /* Stat Grid */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        @media(max-width:860px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px 22px;
            border: 1px solid #e5e7eb;
            transition: transform .15s, box-shadow .15s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .stat-icon svg {
            width: 18px;
            height: 18px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .chart-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .chart-card-header h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .pill-filter {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .pill-opt {
            padding: 5px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            color: var(--text-mid);
            transition: all .15s;
            white-space: nowrap;
            font-family: 'Inter', sans-serif;
        }

        .pill-opt:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .pill-opt.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }


        /* Chart Cards & Layout Fix */
        .chart-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            position: relative;
        }

        .chart-wrapper {
             position: relative;
             width: 100%;
             min-height: 200px;
        }


        /* Bottom Row */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 16px;
        }

        @media(max-width:860px) {
            .bottom-row {
                grid-template-columns: 1fr;
            }
        }

        .klien-card {
            background: linear-gradient(135deg, #111827, #1f2937);
            color: #fff;
            border-radius: 14px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .klien-val {
            font-size: 36px;
            font-weight: 700;
            color: #fff;
        }

        .klien-label {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: #f9fafb;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .activity-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .activity-sub {
            font-size: 12px;
            color: var(--text-muted);
        }

        .activity-badge {
            padding: 3px 9px;
            border-radius: 14px;
            font-size: 11.5px;
            font-weight: 600;
            margin-left: auto;
            white-space: nowrap;
        }
    </style>

    <!-- HERO -->
    <div class="hero-section">
        <div class="hero-bg"></div>
        <div class="hero-fade"></div>
        <div class="hero-content">
            <h2>Kantor Notaris &amp; PPAT HUGO</h2>
            <p>Memberikan layanan hukum notaris terpercaya. Spesialis pengurusan PT, CV, akta perjanjian, dan sertifikasi
                properti.</p>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div style="position:relative;">
        <!-- Skeleton for stat cards -->
        <div id="skeleton-stats" class="stat-grid-skeleton" style="margin-bottom:28px;">
            @for($i=0;$i<4;$i++)
            <div class="skeleton-card">
                <div class="skeleton skeleton-avatar" style="width:40px;height:40px;border-radius:10px;margin-bottom:12px;"></div>
                <div class="skeleton skeleton-line" style="width:50%;height:28px;margin-bottom:8px;"></div>
                <div class="skeleton skeleton-line" style="width:70%;"></div>
            </div>
            @endfor
        </div>
        <!-- Real stat cards (hidden until loaded) -->
        <div id="real-stats" style="display:none;">
        <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;"><svg viewBox="0 0 24 24" fill="none" stroke="#d97706"
                    stroke-width="2">
                    <path d="M14 2H6a2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg></div>
            <div class="stat-value">{{ $totalCases }}</div>
            <div class="stat-label">Total Kasus</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;"><svg viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
                    stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" />
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                </svg></div>
            <div class="stat-value">{{ $ptCases }}</div>
            <div class="stat-label">Kasus PT</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;"><svg viewBox="0 0 24 24" fill="none" stroke="#22c55e"
                    stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                </svg></div>
            <div class="stat-value">{{ $cvCases }}</div>
            <div class="stat-label">Kasus CV</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce7f3;"><svg viewBox="0 0 24 24" fill="none" stroke="#ec4899"
                    stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg></div>
            <div class="stat-value">{{ $pribadiCases }}</div>
            <div class="stat-label">Kasus Pribadi</div>
        </div>
    </div>

        </div><!-- /stat-grid -->
        </div><!-- /real-stats -->
    </div><!-- /stat wrapper -->

    <!-- CHARTS AREA -->
    <div style="position:relative;">
        <!-- Skeleton for charts -->
        <div id="skeleton-charts" class="chart-grid-skeleton" style="margin-bottom:24px;">
            <div class="skeleton-card"><div class="skeleton skeleton-chart" style="height:120px;"></div></div>
            <div class="skeleton-split-grid">
                <div class="skeleton-card"><div class="skeleton skeleton-chart" style="height:140px;"></div></div>
                <div class="skeleton-card"><div class="skeleton skeleton-chart" style="height:140px;"></div></div>
            </div>
        </div>
        <!-- Real charts (hidden until loaded) -->
        <div id="real-charts" style="display:none;">
            <!-- TOP ROW: LINE CHART FULL WIDTH -->
            <div class="chart-card" style="margin-bottom: 24px;">
                <div class="chart-card-header">
                    <h3>Kasus per Tahun</h3>
                    <div class="pill-filter" id="year-pills">
                        <button class="pill-opt active" data-years="5"
                            onclick="setPillActive(this,'year-pills'); updateLineChart(5)">5 Tahun</button>
                        <button class="pill-opt" data-years="3" onclick="setPillActive(this,'year-pills'); updateLineChart(3)">3
                            Tahun</button>
                        <button class="pill-opt" data-years="10"
                            onclick="setPillActive(this,'year-pills'); updateLineChart(10)">10 Tahun</button>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="lineChart" height="100"></canvas>
                </div>
            </div>

            <!-- BOTTOM CHART ROW: BAR & DONUT -->
            <div class="chart-split-grid" style="margin-bottom: 24px;">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>Aktivitas Bulanan</h3>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="barChart" height="180"></canvas>
                    </div>
                </div>
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>Status Kasus</h3>
                    </div>
                    <div class="chart-wrapper" style="display:flex; flex-direction:column; align-items:center;">
                        <canvas id="donutChart" height="200"></canvas>
                        <div id="donut-legend" style="width:100%; display:flex;flex-direction:column;gap:8px;margin-top:14px;"></div>
                    </div>
                </div>
            </div>

        </div><!-- /real-charts -->
    </div><!-- /charts wrapper -->

    <!-- BOTTOM ROW -->
    <div class="bottom-row">
        <div class="chart-card">
            <div class="chart-card-header">
                <h3>Aktivitas Terbaru</h3>
            </div>
            @foreach ($recentCases as $case)
                @php
                    $colors = ['selesai' => '#22c55e', 'proses' => '#f59e0b', 'tertunda' => '#ef4444'];
                    $c = $colors[$case->status] ?? '#9ca3af';
                @endphp
                <div class="activity-item">
                    <span class="activity-dot" style="background:{{ $c }};"></span>
                    <div style="flex:1;min-width:0;">
                        <div class="activity-name">{{ $case->client_name }}</div>
                        <div class="activity-sub">
                            {{ $case->type }} • {{ $case->deadline?->format('d/m/Y') }}
                            @if($case->phone) • <span style="color:#6b7280;">+62 {{ ltrim($case->phone, '0') }}</span> @endif
                        </div>
                    </div>
                    <span class="activity-badge"
                        style="background:{{ $c }}22;color:{{ $c }};">{{ ucfirst($case->status) }}</span>
                </div>
            @endforeach
        </div>
        <div class="klien-card">
            <div style="font-size:36px;"></div>
            <div>
                <div class="klien-val">{{ $totalClients }}</div>
                <div class="klien-label">Total Klien</div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ── Skeleton Reveal ──────────────────────────────────────────────
            window.addEventListener('DOMContentLoaded', function() {
                // Small delay so shimmer is briefly visible
                setTimeout(function() {
                    var sStats = document.getElementById('skeleton-stats');
                    var rStats = document.getElementById('real-stats');
                    var sCharts = document.getElementById('skeleton-charts');
                    var rCharts = document.getElementById('real-charts');
                    
                    if (sStats) { 
                        sStats.style.transition = 'opacity 0.3s'; 
                        sStats.style.opacity = '0'; 
                        setTimeout(() => { 
                            sStats.style.display = 'none'; 
                            if(rStats) rStats.style.display = 'block'; 
                        }, 300); 
                    }
                    if (sCharts) { 
                        sCharts.style.transition = 'opacity 0.3s'; 
                        sCharts.style.opacity = '0'; 
                        setTimeout(() => { 
                            sCharts.style.display = 'none'; 
                            if(rCharts) {
                                rCharts.style.display = 'block';
                                // Initialize charts only when visible
                                buildLineChart(5);
                                initDonutChart();
                                initBarChart();
                            }
                        }, 300); 
                    }
                }, 500);
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <script>
            var yearlyDataRaw = @json($yearlyData);

            function setPillActive(el, groupId) {
                document.querySelectorAll(`#${groupId} .pill-opt`).forEach(b => b.classList.remove('active'));
                el.classList.add('active');
            }

            // Build yearly data sets
            var allLabels = Object.keys(yearlyDataRaw);
            var allData = Object.values(yearlyDataRaw);

            let lineChart;

            function buildLineChart(n) {
                const labels = allLabels.slice(-n);
                const data = allData.slice(-n);
                const ctx = document.getElementById('lineChart').getContext('2d');
                if (lineChart) lineChart.destroy();

                // Create vibrant gradient fill
                var lineGrad = ctx.createLinearGradient(0, 0, 0, ctx.canvas.clientHeight || 200);
                lineGrad.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
                lineGrad.addColorStop(0.5, 'rgba(168, 85, 247, 0.15)');
                lineGrad.addColorStop(1, 'rgba(236, 72, 153, 0.02)');

                lineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Kasus',
                            data,
                            borderColor: '#6366f1',
                            borderWidth: 3,
                            backgroundColor: lineGrad,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#6366f1',
                            pointBorderWidth: 2.5,
                            pointRadius: 6,
                            pointHoverRadius: 9,
                            pointHoverBackgroundColor: '#6366f1',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: '#f3f4f6'
                                },
                                ticks: {
                                    font: {
                                        family: 'Inter',
                                        size: 11
                                    },
                                    color: '#374151'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f3f4f6'
                                },
                                ticks: {
                                    font: {
                                        family: 'Inter',
                                        size: 11
                                    },
                                    color: '#374151',
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
            // buildLineChart(5); // Called via skeleton reveal


            function updateLineChart(n) {
                buildLineChart(n);
            }

            // Donut
            function initDonutChart() {
                const donutCtx = document.getElementById('donutChart').getContext('2d');

            const donutCounts = {
                Selesai: {{ $selesai }},
                Proses: {{ $proses }},
                Tertunda: {{ $tertunda }}
            };
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(donutCounts),
                    datasets: [{
                        data: Object.values(donutCounts),
                        backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                        hoverBackgroundColor: ['#059669', '#d97706', '#e11d48'],
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverOffset: 10,
                        borderRadius: 4
                    }]
                },
                options: {
                    cutout: '68%',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            const legend = document.getElementById('donut-legend');
            [
                ['Selesai', '#10b981'],
                ['Proses', '#f59e0b'],
                ['Tertunda', '#f43f5e']
            ].forEach(([l, c], i) => {
                const v = Object.values(donutCounts)[i];
                legend.innerHTML += `<div style="display:flex;align-items:center;gap:8px;font-size:12.5px;">
        <span style="width:10px;height:10px;border-radius:50%;background:${c};flex-shrink:0;box-shadow:0 0 6px ${c}44;"></span>
        <span style="color:#374151;flex:1;">${l}</span>
        <span style="font-weight:700;color:#1f2937;">${v}</span></div>`;
                });
            }

            // Bar Chart (Monthly Activity)
            function initBarChart() {
                const barCtx = document.getElementById('barChart').getContext('2d');
                var barColors = [
                    '#6366f1', '#8b5cf6', '#a855f7', '#d946ef',
                    '#ec4899', '#f43f5e', '#f97316', '#f59e0b',
                    '#eab308', '#22c55e', '#14b8a6', '#06b6d4'
                ];
                var barHoverColors = [
                    '#4f46e5', '#7c3aed', '#9333ea', '#c026d3',
                    '#db2777', '#e11d48', '#ea580c', '#d97706',
                    '#ca8a04', '#16a34a', '#0d9488', '#0891b2'
                ];
                new Chart(barCtx, {

                type: 'bar',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [{
                        label: 'Kasus Baru',
                        data: @json($monthlyData),
                        backgroundColor: barColors,
                        hoverBackgroundColor: barHoverColors,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, font: { family: 'Inter', size:11 }, color: '#374151' }, grid: { color: '#f3f4f6' } },
                        x: { ticks: { font: { family: 'Inter', size:11 }, color: '#374151' }, grid: { display: false } }
                    }
                }
            });
            }

        </script>
    @endpush

    @if (!empty($pwdResetNotification))
        @push('scripts')
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    // Show password reset notification in Chat Assist
                    const msgs = document.getElementById('chat-messages');
                    if (msgs) {
                        const wrap = document.createElement('div');
                        wrap.className = 'bubble-wrap';
                        wrap.innerHTML = `<div class="bubble them" style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;font-size:12.5px;max-width:100%;">
                    <strong>Notifikasi Sistem</strong><br>{{ addslashes($pwdResetNotification) }}
                </div>`;
                        msgs.appendChild(wrap);
                        // Show badge
                        const badge = document.getElementById('chat-badge');
                        if (badge) badge.style.display = 'block';
                    }
                });
            </script>
        @endpush
    @endif

@endsection
