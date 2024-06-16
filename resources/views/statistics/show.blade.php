@extends('layouts.admin')

@section('header-title', 'Statistics')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            <h2 class="text-3xl font-bold mb-6">Statistics</h2>
            <div class="flex mb-8">
                <div class="w-1/3 p-4 bg-gray-200 dark:bg-gray-800 rounded-lg mr-4">
                    <div class="text-center mb-4">
                        <h3 class="text-2xl font-bold mb-4">Overall Stats</h3>
                        <p id="totalTickets" class="text-lg mb-2">Total Tickets: Loading...</p>
                        <p id="totalRevenue" class="text-lg mb-2">Total Revenue: Loading...</p>
                        <p id="averageRevenue" class="text-lg">Average Revenue: Loading...</p>
                    </div>
                    <hr class="my-4 border-gray-300 dark:border-gray-600">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold mb-4">Edit Configuration</h3>
                        <form method="POST" action="{{ route('config.update') }}" class="space-y-4">
                            @csrf
                            <div class="space-y-2">
                                <x-field.input name="ticket_price" label="Ticket Price" :readonly="false" value="{{ old('ticket_price', $configuration->ticket_price) }}"/>
                            </div>
                            <div class="space-y-2">
                                <x-field.input name="registered_customer_ticket_discount" label="Registered Customer Ticket Discount" :readonly="false" value="{{ old('registered_customer_ticket_discount', $configuration->registered_customer_ticket_discount) }}"/>
                            </div>
                            <button type="submit" class="w-full p-2 bg-blue-500 text-white rounded hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 mt-4">Save</button>
                        </form>
                    </div>
                </div>
                <div class="w-2/3">
                    <canvas id="salesByYearChart" width="1600" height="800"></canvas>
                </div>
            </div>
            <div class="mb-8 flex space-x-4">
                <div class="w-1/2">
                    <h3 class="text-2xl font-bold mb-4">Top Movies Last Year</h3>
                    <table id="topMoviesLastYearTable" class="w-full text-center table-fixed">
                        <thead class="bg-gray-200 text-black dark:bg-gray-800 dark:text-gray-50">
                            <tr>
                                <th class="px-4 py-2 w-1/12">Rank</th>
                                <th class="px-4 py-2 w-8/12">Movie Title</th>
                                <th class="px-4 py-2 w-3/12">Tickets Sold</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-100 dark:bg-gray-700">
                        </tbody>
                    </table>
                </div>
                <div class="w-1/2">
                    <h3 class="text-2xl font-bold mb-4">Top Movies This Year</h3>
                    <table id="topMoviesThisYearTable" class="w-full text-center table-fixed">
                        <thead class="bg-gray-200 text-black dark:bg-gray-800 dark:text-gray-50">
                            <tr>
                                <th class="px-4 py-2 w-1/12">Rank</th>
                                <th class="px-4 py-2 w-8/12">Movie Title</th>
                                <th class="px-4 py-2 w-3/12">Tickets Sold</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-100 dark:bg-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mb-8 flex space-x-4">
                <div class="w-1/2">
                    <h3 class="text-2xl font-bold mb-4">Top Genres by Tickets Sold</h3>
                    <canvas id="topGenresChart" width="800" height="400"></canvas>
                </div>
                <div class="w-1/2">
                    <h3 class="text-2xl font-bold mb-4">Top Theaters by Tickets Sold</h3>
                    <canvas id="topTheatersChart" width="800" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        async function fetchData(url) {
            try {
                const response = await fetch(url);
                const data = await response.json();
                console.log('Data received:', data);
                return data;
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function formatNumberWithSpace(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        async function loadOverallStats() {
            const stats = await fetchData('{{ route('statistics.overallStats') }}');
            if (stats) {
                document.getElementById('totalTickets').textContent = `Total Tickets: ${formatNumberWithSpace(stats.total_tickets)}`;
                document.getElementById('totalRevenue').textContent = `Total Revenue: $${formatNumberWithSpace(parseFloat(stats.total_revenue))}`;
                if (stats.average_revenue !== null) {
                    document.getElementById('averageRevenue').textContent = `Average Revenue: $${Number(stats.average_revenue).toFixed(2).toLocaleString('en-US').replace(/,/g, ' ')}`;
                } else {
                    document.getElementById('averageRevenue').textContent = 'Average Revenue: $0.00';
                }
            }
        }

        async function createSalesByYearChart() {
            const data = await fetchData('{{ route('statistics.salesByYear') }}');
            if (!data || !Array.isArray(data)) {
                console.error('Invalid data format:', data);
                return;
            }

            const labels = data.map(item => item.year).filter(year => year !== null);
            const totalTickets = data.map(item => item.total_tickets).filter((_, index) => data[index].year !== null);
            const totalRevenue = data.map(item => item.total_revenue).filter((_, index) => data[index].year !== null);

            const maxTickets = Math.max(...totalTickets) + 100000;
            const maxRevenue = Math.max(...totalRevenue) + 2000000;

            const ctx = document.getElementById('salesByYearChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Tickets',
                            data: totalTickets,
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Total Revenue',
                            data: totalRevenue,
                            backgroundColor: 'rgba(255, 99, 132, 0.8)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            yAxisID: 'revenueAxis'
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: maxTickets,
                            type: 'logarithmic',
                            title: {
                                display: true,
                                text: 'Total Tickets',
                                font: {
                                    size: 18
                                }
                            },
                            ticks: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        revenueAxis: {
                            beginAtZero: true,
                            max: maxRevenue,
                            type: 'logarithmic',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Revenue',
                                font: {
                                    size: 18
                                }
                            },
                            ticks: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    layout: {
                        padding: 20
                    }
                }
            });
        }

        async function createTopMoviesTable() {
            const lastYearData = await fetchData('{{ route('statistics.topMoviesLastYear') }}');
            const thisYearData = await fetchData('{{ route('statistics.topMoviesThisYear') }}');

            function populateTable(tableId, data) {
                const tableBody = document.querySelector(`#${tableId} tbody`);
                tableBody.innerHTML = '';

                data.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2">${index + 1}</td>
                        <td class="px-4 py-2">${item.movie_title}</td>
                        <td class="px-4 py-2">${item.total_tickets}</td>
                    `;
                    tableBody.appendChild(row);
                });
            }

            if (lastYearData && Array.isArray(lastYearData)) {
                populateTable('topMoviesLastYearTable', lastYearData);
            }

            if (thisYearData && Array.isArray(thisYearData)) {
                populateTable('topMoviesThisYearTable', thisYearData);
            }
        }

        async function createTopGenresChart() {
            const data = await fetchData('{{ route('statistics.topGenres') }}');
            if (!data || !Array.isArray(data)) {
                console.error('Invalid data format:', data);
                return;
            }

            const labels = data.map(item => item.genre);
            const totalTickets = data.map(item => item.total_tickets);

            const ctx = document.getElementById('topGenresChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: totalTickets,
                        backgroundColor: 'rgba(61, 55, 232, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `Tickets Sold: ${context.raw}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        async function createTopTheatersChart() {
            const data = await fetchData('{{ route('statistics.topTheaters') }}');
            if (!data || !Array.isArray(data)) {
                console.error('Invalid data format:', data);
                return;

            }

            const labels = data.map(item => item.theater);
            const totalTickets = data.map(item => item.total_tickets);

            const ctx = document.getElementById('topTheatersChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: totalTickets,
                        backgroundColor: 'rgba(48, 135, 33, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `Tickets Sold: ${context.raw}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        loadOverallStats();
        createSalesByYearChart();
        createTopMoviesTable();
        createTopGenresChart();
        createTopTheatersChart();
    </script>
@endsection
