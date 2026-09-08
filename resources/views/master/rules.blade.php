<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aturan Disiplin & Prestasi - Kesiswaan & BK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5F9FA] text-[#182A3C] min-h-full antialiased font-sans">

@include('layouts.sidebar')

<main class="flex-1 overflow-y-auto p-6 md:p-8">
    <div class="max-w-6xl mx-auto">

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-[#182A3C]">Aturan Disiplin & Prestasi</h2>
                <div class="w-8 h-8 rounded-full bg-[#044A87] text-white flex items-center justify-center text-sm font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>

            <!-- Violation Rules -->
            <div>
                <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Aturan Pelanggaran</h3>
                <div="space-y-2">
                    @foreach ($violationRules as $rule)
                        <div class="bg-[#F5F9FA] rounded-xl p-4 border-l-4 border-{{ str_replace('#', '', $rule->severity === 'khusus' ? 'rose-500' : ($rule->severity === 'berat' ? 'red-500' : ($rule->severity === 'sedang' ? 'amber-500' : 'emerald-500')) }})">
                            <div class="flex items-between justify-between">
                                <div>
                                    <p class="font-medium text-[#182A3C]">{{ $rule->code }} - {{ $rule->name }}</p>
                                    <p class="text-xs text-[#64748B]">{{ $rule->description }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-{{ str_replace('#', '', $rule->severity === 'khusus' ? 'rose-600' : ($rule->severity === 'berat' ? 'red-600' : ($rule->severity === 'sedang' ? 'amber-600' : 'emerald-600')) }})">{{ number_format($rule->points) }} poin</p>
                                    <p class="text-[10px] uppercase text-{{ str_replace('#', '', $rule->severity === 'khusus' ? 'rose-400' : ($rule->severity === 'berat' ? 'red-400' : ($rule->severity === 'sedang' ? 'amber-400' : 'emerald-400')) }})">{{ $rule->severity }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($violationRules->isEmpty())
                        <p class="text-xs text-[#9CA3FN]">Belum ada aturan pelanggaran.</p>
                    @endif
                </div>
            </div>

            <!-- Achievement Rules -->
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-[#182A3C] mb-3">Aturan Prestasi</h3>
                <div="space-y-2">
                    @foreach ($achievementRules as $rule)
                        <div class="bg-[#F5F9FA] rounded-xl p-4 border-l-4 border-{{ str_replace('#', '', $rule->pillar === 'Prestasi Kompetisi' ? 'emerald-500' : ($rule->pillar === 'Organisasi & Kepemimpinan' ? 'amber-500' : 'blue-500')) }})">
                            <div class="flex items-between justify-between">
                                <div>
                                    <p class="font-medium text-[#182A3C]">{{ $rule->code }} - {{ $rule->name }}</p>
                                    <p class="text-xs text-[#64748B]">{{ $rule->description }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-{{ str_replace('#', '', $rule->pillar === 'Prestasi Kompetisi' ? 'emerald-600' : ($rule->pillar === 'Organisasi & Kepemimpinan' ? 'amber-600' : 'blue-600')) }})">{{ number_format($rule->points) }} poin</p>
                                    <p class="text-[10px] uppercase text-{{ str_replace('#', '', $rule->pillar === 'Prestasi Kompetisi' ? 'emerald-400' : ($rule->pillar === 'Organisasi & Kepemimpinan' ? 'amber-400' : 'blue-400')) }})">{{ $rule->pillar }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($achievementRules->isEmpty())
                        <p class="text-xs text-[#9CA3FN]">Belum ada aturan prestasi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>