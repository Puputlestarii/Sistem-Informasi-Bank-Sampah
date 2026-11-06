<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MasJOS - Masyarakat Jogja Olah Sampah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-fredoka { font-family: 'Fredoka One', cursive; }
        html { scroll-padding-top: 80px; scroll-behavior: smooth; }
        .bg-cream { background-color: #F9F7F3; }
        .btn-masjos { background-color: #d93d2b; }
        .btn-masjos:hover { background-color: #b92c1a; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="bg-cream shadow-md fixed w-full top-0 z-50">
        <nav class="container mx-auto px-6 py-3 flex justify-between items-center">
            <a href="#" class="flex items-center space-x-2">
                <img src="<?= base_url('images/logo_baru.png') ?>" alt="Logo MasJOS" class="h-10 w-auto">
                <span class="text-2xl font-fredoka" style="color: #d93d2b;">MasJOS</span>
            </a>
            <div class="relative">
                <button id="hamburger-button" class="text-gray-800 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
                <div id="mobile-menu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-md shadow-xl z-20 py-2">
                    <div class="px-4 py-1">
                         <a href="<?= base_url('auth') ?>" class="block text-center btn-masjos text-white font-semibold px-4 py-2 rounded-lg transition duration-300 w-full">Login</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section id="hero" class="min-h-screen flex items-center bg-white pt-24 overflow-hidden">
            <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="md:w-1/2 flex justify-center relative">
                    <img src="<?= base_url('images/masjos_maskot.png') ?>" alt="Maskot MasJOS" class="w-full max-w-xs relative z-10">
                </div>
                <div class="md:w-1/2 text-center md:text-left">
                    <h1 class="text-5xl md:text-7xl font-fredoka" style="color: #d93d2b;">MasJOS</h1>
                    <p class="mt-2 text-3xl font-bold tracking-wide">
                        <span style="color: #d93d2b;">Masyarakat</span>
                        <span style="color: #0d6032;">Jogja</span>
                        <span style="color: #d93d2b;">Olah</span>
                        <span style="color: #38a169;">Sampah</span>
                    </p>
                    <p class="mt-6 text-gray-600 max-w-lg mx-auto md:mx-0">Sebuah gerakan untuk memberdayakan masyarakat Yogyakarta dalam mengelola sampah dari rumah.</p>
                    <a href="<?= base_url('auth') ?>" class="mt-8 inline-block bg-green-600 text-white font-bold py-3 px-8 rounded-full text-lg hover:bg-green-700 transition duration-300">Mulai Sekarang</a>
                </div>
            </div>
        </section>

        <section id="edukasi" class="py-20 bg-gray-100">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">
                    <?php 
                    $edu = [
                        ['img' => 'edukasi-pilah.png', 'title' => 'Pilah Sampah'],
                        ['img' => 'edukasi-setor.png', 'title' => 'Setor Anorganik'],
                        ['img' => 'edukasi-olah.png', 'title' => 'Olah Organik'],
                        ['img' => 'edukasi-habiskan.png', 'title' => 'Habiskan Makanan'],
                        ['img' => 'edukasi-wadah.png', 'title' => 'Wadah Berulang']
                    ];
                    foreach($edu as $item): ?>
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center text-center">
                        <img src="<?= base_url('images/'.$item['img']) ?>" alt="<?= $item['title'] ?>" class="h-28 w-auto mb-4">
                        <h3 class="font-semibold text-gray-800"><?= $item['title'] ?></h3>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        </main>

    <script>
        const hb = document.getElementById('hamburger-button');
        const mm = document.getElementById('mobile-menu');
        hb.addEventListener('click', () => mm.classList.toggle('hidden'));
    </script>
</body>
</html>