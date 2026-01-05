<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            400: '#FCA5A5', // Placeholder if not exact match, but we use hex below
                            500: '#ED6F55', // Vidadu Orange
                            600: '#DC644B',
                        },
                        dark: {
                            800: '#1F2937',
                            900: '#111827',
                            950: '#030712', // Rich Black
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @page { margin: 0; size: landscape; }
        html, body { margin: 0; padding: 0; width: 100%; height: 100%; font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact; }
    </style>
</head>
<body class="bg-white text-white flex items-center justify-center relative p-6 box-border overflow-hidden">
    
    <!-- Certificate Card Container (The Dark Element) -->
    <div class="relative w-full h-full bg-[#030712] rounded-[2rem] overflow-hidden flex flex-col shadow-2xl">
        
        <!-- Background Elements - Strictly Clipped inside Card -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            <!-- Grid Pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
            
            <!-- Glow Orbs -->
            <div class="absolute -top-[300px] -left-[300px] w-[800px] h-[800px] bg-[#ED6F55] rounded-full blur-[150px] opacity-10"></div>
            <div class="absolute -bottom-[300px] -right-[300px] w-[800px] h-[800px] bg-[#ED6F55] rounded-full blur-[150px] opacity-10"></div>
        </div>

    <!-- Main Content wrapper inside Card -->
    <div class="relative z-10 w-full h-full flex flex-col">
        <!-- Inner Border (Subtle) -->
        <div class="absolute inset-4 border border-gray-800/50 rounded-2xl pointer-events-none"></div>

            <!-- Top Decoration Line -->
            <div class="absolute top-0 left-0 right-0 h-2 bg-[#ED6F55]"></div>

            <!-- Logo / Brand -->
            <div class="mt-12 mb-10 text-center">
                <h1 class="text-3xl font-black tracking-tighter uppercase text-white mb-2">
                    Vidadu<span class="text-[#ED6F55]">Academy</span>
                </h1>
                <div class="text-xs font-bold tracking-[0.3em] text-gray-500 uppercase">Official Certificate of Completion</div>
            </div>

            <!-- Main Content - Centered Vertically in remaining space -->
            <div class="flex-1 flex flex-col justify-center w-full max-w-4xl space-y-6 -mt-8">
                
                <!-- Presented To -->
                <div class="space-y-4 text-center">
                    <p class="text-gray-400 font-extralight text-lg uppercase tracking-widest">Týmto potvrdzujeme, že</p>
                    <h2 class="text-6xl font-black tracking-tight text-white mb-2 leading-none">{{ $student_name }}</h2>
                </div>

                <!-- Course Info -->
                <div class="py-8 relative text-center">
                    <!-- Divider Lines (Solid) -->
                    <div class="w-1/2 mx-auto h-px bg-gray-800 mb-6"></div>
                    
                    <p class="text-gray-300 font-light mb-2">úspešne absolvoval(a) kurz</p>
                    <!-- Removed text-gradient to prevent rendering artifact -->
                    <h3 class="text-4xl font-bold text-[#ED6F55] leading-tight px-12 pb-4">
                        {{ $course_title }}
                    </h3>

                    <div class="w-1/2 mx-auto h-px bg-gray-800 mt-2"></div>
                </div>

            </div>

            <!-- Footer / Signatures -->
            <div class="grid grid-cols-3 w-full max-w-5xl items-end px-12 pb-10">
                
                <!-- Date -->
                <div class="text-left">
                    <div class="text-2xl font-bold text-white mb-1">{{ $date }}</div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-bold">Dátum dokončenia</div>
                </div>

                <!-- Badge/Seal (Center) -->
                <div class="flex justify-center relative translate-y-4">
                    <!-- Removed shadow and glow to prevent solid block rendering -->
                    <div class="w-28 h-28 rounded-full border-[6px] border-[#ED6F55] flex items-center justify-center bg-[#030712]">
                        <svg class="w-14 h-14 text-[#ED6F55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Instructor -->
                <div class="text-right">
                    <div class="text-2xl font-bold text-white mb-1" style="font-family: 'Inter', cursive;">Vidadu Team</div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-bold">Hlavný inštruktor</div>
                </div>

            </div>

            <!-- ID -->
            <div class="absolute bottom-3 right-6 text-[9px] text-gray-600 font-mono tracking-widest">
                ID: {{ $certificate_id }}
            </div>

    </div>
</body>
</html>
