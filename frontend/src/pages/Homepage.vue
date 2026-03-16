<template>
  <div class="min-h-screen bg-white flex flex-col">
    <!-- Fixed Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/97 backdrop-blur-md border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-12">
        <div class="flex justify-between items-center h-24">
          <!-- Hotel Name on the left -->
          <div class="flex-shrink-0 flex items-center gap-4">
            <img src="/Joannaslogo.png" alt="Joanna's Logo" class="h-20 w-20 object-contain" />
            <div class="leading-tight">
              <div class="text-base font-light tracking-[0.22em] text-gray-900 uppercase">Joanna's Nook</div>
              <div class="text-xs text-gray-400 tracking-[0.4em] uppercase mt-1">Bed &amp; Breakfast</div>
            </div>
          </div>

          <!-- Navigation links on the right -->
          <nav class="hidden md:flex items-center gap-10">
            <a href="#home" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase transition duration-300">Home</a>
            <a href="#about" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase transition duration-300">About Us</a>
            <a href="#gallery" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase transition duration-300">Gallery</a>
            <a href="#services" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase transition duration-300">Services</a>
            <a href="#contact" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase transition duration-300">Contact</a>
            <button
              @click="navigateToLogin"
              class="text-[11px] border border-green-800 text-green-800 hover:bg-green-800 hover:text-white font-light py-2.5 px-7 transition duration-300 tracking-[0.18em] uppercase"
            >
              Staff Login
            </button>
          </nav>

          <!-- Mobile hamburger -->
          <button @click="mobileNavOpen = !mobileNavOpen" class="md:hidden flex items-center justify-center p-2 text-gray-600 hover:text-green-800 transition-colors" aria-label="Toggle menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile nav menu -->
      <div v-if="mobileNavOpen" class="md:hidden absolute top-full left-0 right-0 bg-white border-t border-gray-100 px-6 py-5 flex flex-col gap-5 shadow-lg z-50 overflow-y-auto max-h-[calc(100vh-6rem)]">
        <a href="#home" @click="mobileNavOpen=false" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase py-2 border-b border-gray-50 transition">Home</a>
        <a href="#about" @click="mobileNavOpen=false" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase py-2 border-b border-gray-50 transition">About Us</a>
        <a href="#gallery" @click="mobileNavOpen=false" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase py-2 border-b border-gray-50 transition">Gallery</a>
        <a href="#services" @click="mobileNavOpen=false" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase py-2 border-b border-gray-50 transition">Services</a>
        <a href="#contact" @click="mobileNavOpen=false" class="text-[11px] text-gray-500 hover:text-green-800 font-light tracking-[0.18em] uppercase py-2 border-b border-gray-50 transition">Contact</a>
        <button @click="navigateToLogin(); mobileNavOpen=false" class="text-[11px] border border-green-800 text-green-800 font-light py-3 px-6 tracking-[0.18em] uppercase text-center transition">Staff Login</button>
      </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative h-[540px] md:h-[740px] lg:h-[88vh] flex items-center justify-center overflow-hidden">
      <!-- Ken Burns background -->
      <div class="hero-bg absolute inset-0 bg-cover bg-center" style="background-image: url('/Backimagehomepage.jpg');"></div>
      <!-- Gradient overlay -->
      <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0.62) 100%);"></div>

      <!-- Shimmer line top -->
      <div class="hero-shimmer-line"></div>

      <div class="relative text-center px-6 max-w-4xl mx-auto">
        <h1 class="text-4xl sm:text-5xl md:text-[64px] lg:text-[76px] font-extralight text-white mb-5 md:mb-7 tracking-wide leading-[1.12] hero-anim hero-anim--up" style="--h-delay:450ms">
          Experience<br class="hidden sm:block" /><em class="font-light not-italic"> Comfort &amp; Luxury</em>
        </h1>
        <!-- Animated decorative accent line under heading -->
        <div class="hero-accent-line mx-auto mb-8" style="--h-delay:750ms"></div>
        <p class="text-xs md:text-sm text-white/75 mb-12 font-light leading-loose tracking-[0.2em] max-w-lg mx-auto uppercase hero-anim hero-anim--up" style="--h-delay:850ms">
          Your sanctuary of relaxation — premium accommodations &amp; world-class hospitality
        </p>
        <button
          @click="scrollToSection('services')"
          class="hero-anim hero-anim--up inline-block border border-white/75 text-white hover:bg-white hover:text-green-900 font-light py-4 px-14 transition duration-500 text-[11px] tracking-[0.3em] uppercase"
          style="--h-delay:1050ms"
        >
          Explore Our Services
        </button>
      </div>

      <!-- Scroll indicator -->
      <div class="absolute bottom-9 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5 hero-scroll-indicator">
        <span class="text-white/40 text-[9px] tracking-[0.35em] uppercase mb-1">Scroll</span>
        <div class="hero-scroll-line"></div>
        <svg class="w-3 h-3 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
      </div>
    </section>

    <!-- ── About Us Section ──────────────────────────────────────────────── -->
    <section id="about" class="py-28 bg-white border-t border-gray-100 overflow-hidden">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Title -->
        <div class="text-center mb-20 about-reveal about-reveal--up" style="--delay:0ms">
          <p class="text-[11px] tracking-[0.45em] text-gray-400 uppercase mb-4">Our Story</p>
          <h2 class="text-5xl font-extralight text-gray-800 tracking-[0.15em] uppercase mb-6">About Us</h2>
          <div class="flex items-center justify-center gap-4">
            <div class="h-px w-16 bg-gray-200"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-green-700"></div>
            <div class="h-px w-16 bg-gray-200"></div>
          </div>
        </div>

        <!-- Two-column layout: Text + Image -->
        <div class="flex flex-col lg:flex-row items-center gap-16 mb-24">

          <!-- Text -->
          <div class="flex-1">
            <p class="text-xs tracking-[0.35em] text-amber-600 uppercase mb-4 about-reveal about-reveal--left" style="--delay:100ms">Welcome to Joanna's Nook</p>
            <h3 class="text-3xl font-extralight text-green-800 tracking-wide mb-7 leading-snug about-reveal about-reveal--left" style="--delay:200ms">
              A Home Away<br />From Home
            </h3>
            <div class="h-px w-12 bg-gray-200 mb-7 about-reveal about-reveal--left" style="--delay:260ms"></div>
            <p class="text-base text-gray-500 font-light leading-loose mb-5 about-reveal about-reveal--left" style="--delay:320ms">
              Nestled in the heart of Balingasag, Misamis Oriental, Joanna's Nook Bed &amp; Breakfast is a cozy and welcoming retreat that blends warm Filipino hospitality with modern comforts. We take pride in providing our guests with a relaxing and memorable stay.
            </p>
            <p class="text-base text-gray-500 font-light leading-loose mb-5 about-reveal about-reveal--left" style="--delay:420ms">
              Whether you're visiting for leisure, business, or celebrating a special occasion, our dedicated team is committed to making every moment of your stay exceptional. From our thoughtfully furnished rooms to our event facilities, everything at Joanna's Nook is designed with your comfort in mind.
            </p>
            <p class="text-base text-gray-500 font-light leading-loose about-reveal about-reveal--left" style="--delay:520ms">
              We believe that great hospitality starts with genuine care — and that's exactly what we offer every single guest, every single day.
            </p>
            <div class="mt-10 flex flex-wrap gap-4 about-reveal about-reveal--up" style="--delay:620ms">
              <button @click="scrollToSection('services')"
                class="border border-green-700 text-green-800 hover:bg-green-700 hover:text-white text-[11px] font-light py-3.5 px-10 tracking-[0.25em] uppercase transition duration-300">
                Explore Our Services
              </button>
              <button @click="scrollToSection('contact')"
                class="border border-gray-300 text-gray-600 hover:border-green-700 hover:text-green-700 text-[11px] font-light py-3.5 px-10 tracking-[0.25em] uppercase transition duration-300">
                Find Us
              </button>
            </div>
          </div>

          <!-- Image -->
          <div class="w-full lg:w-[420px] flex-shrink-0 about-reveal about-reveal--right" style="--delay:200ms">
            <div class="relative about-img-wrapper">
              <!-- Pulsing decorative ring -->
              <div class="about-pulse-ring"></div>
              <img src="/Backimagehomepage.jpg" alt="Joanna's Nook"
                class="relative z-10 w-full h-[380px] object-cover shadow-xl about-img transition-transform duration-700 ease-out" />
              <!-- Decorative border offset -->
              <div class="absolute -bottom-4 -right-4 w-full h-full border border-green-200 -z-10"></div>
              <!-- Floating badge -->
              <div class="absolute -bottom-6 -left-6 z-20 bg-white shadow-xl border border-gray-100 px-5 py-4 about-float-badge">
                <p class="text-[9px] tracking-[0.3em] text-gray-400 uppercase mb-1">Est. in Balingasag</p>
                <p class="text-sm font-light text-green-800 tracking-wider">Misamis Oriental</p>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Narrative Timeline ── -->
        <div class="mb-24 about-reveal about-reveal--up" style="--delay:80ms">
          <div class="text-center mb-12">
            <p class="text-[11px] tracking-[0.45em] text-gray-400 uppercase mb-3">How It All Started</p>
            <h3 class="text-3xl font-extralight text-gray-700 tracking-wider">Our Journey</h3>
          </div>
          <div class="relative flex flex-col md:flex-row items-start md:items-center gap-0 md:gap-0">
            <!-- Horizontal connector line (desktop) -->
            <div class="hidden md:block absolute top-8 left-[calc(16.66%+1rem)] right-[calc(16.66%+1rem)] h-px bg-gray-200 z-0"></div>

            <!-- Step 1 -->
            <div class="flex-1 flex flex-col items-center text-center px-6 about-timeline-step" style="--step-delay:150ms">
              <div class="relative z-10 w-16 h-16 rounded-full bg-green-700 flex items-center justify-center shadow-lg mb-5 about-timeline-icon">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
              </div>
              <p class="text-[10px] tracking-[0.35em] text-amber-500 uppercase mb-2">The Beginning</p>
              <h4 class="text-base font-light text-gray-800 tracking-wider mb-3">A Family Dream</h4>
              <p class="text-sm text-gray-400 font-light leading-loose">Born from a passion for warm hospitality, Joanna's Nook started as a family dream to create a truly welcoming retreat in Balingasag.</p>
            </div>

            <!-- Step 2 -->
            <div class="flex-1 flex flex-col items-center text-center px-6 about-timeline-step" style="--step-delay:300ms">
              <div class="relative z-10 w-16 h-16 rounded-full bg-amber-500 flex items-center justify-center shadow-lg mb-5 about-timeline-icon">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </div>
              <p class="text-[10px] tracking-[0.35em] text-amber-500 uppercase mb-2">Growing Together</p>
              <h4 class="text-base font-light text-gray-800 tracking-wider mb-3">Building Community</h4>
              <p class="text-sm text-gray-400 font-light leading-loose">We expanded our rooms and event packages, welcoming guests from across the region to celebrate life's biggest moments with us.</p>
            </div>

            <!-- Step 3 -->
            <div class="flex-1 flex flex-col items-center text-center px-6 about-timeline-step" style="--step-delay:450ms">
              <div class="relative z-10 w-16 h-16 rounded-full bg-green-700 flex items-center justify-center shadow-lg mb-5 about-timeline-icon">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
              </div>
              <p class="text-[10px] tracking-[0.35em] text-amber-500 uppercase mb-2">Today &amp; Beyond</p>
              <h4 class="text-base font-light text-gray-800 tracking-wider mb-3">Continuing Excellence</h4>
              <p class="text-sm text-gray-400 font-light leading-loose">With modern comforts and a heart full of care, we remain dedicated to giving every guest an experience that feels like home.</p>
            </div>
          </div>
        </div>

        <!-- Stats / Highlights row -->
        <div class="about-stats-grid grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-100 border border-gray-100">
          <div class="bg-white px-8 py-10 text-center about-reveal about-reveal--up" style="--delay:0ms">
            <p class="text-4xl font-extralight text-green-700 mb-2">{{ statRooms }}<span class="text-2xl">+</span></p>
            <p class="text-[11px] tracking-[0.3em] text-gray-400 uppercase">Rooms Available</p>
          </div>
          <div class="bg-white px-8 py-10 text-center about-reveal about-reveal--up" style="--delay:120ms">
            <p class="text-4xl font-extralight text-amber-600 mb-2">{{ statPackages }}<span class="text-2xl">+</span></p>
            <p class="text-[11px] tracking-[0.3em] text-gray-400 uppercase">Event Packages</p>
          </div>
          <div class="bg-white px-8 py-10 text-center about-reveal about-reveal--up" style="--delay:240ms">
            <p class="text-4xl font-extralight text-green-700 mb-2">24<span class="text-2xl">/7</span></p>
            <p class="text-[11px] tracking-[0.3em] text-gray-400 uppercase">Guest Support</p>
          </div>
          <div class="bg-white px-8 py-10 text-center about-reveal about-reveal--up" style="--delay:360ms">
            <p class="text-4xl font-extralight text-amber-600 mb-2">{{ statSatisfaction }}<span class="text-2xl">%</span></p>
            <p class="text-[11px] tracking-[0.3em] text-gray-400 uppercase">Satisfaction Goal</p>
          </div>
        </div>

      </div>
    </section>

    <!-- ── Gallery Section ─────────────────────────────────────────────── -->
    <section id="gallery" class="py-28 bg-gray-50 border-t border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Heading -->
        <div class="text-center mb-20">
          <p class="text-[11px] tracking-[0.45em] text-gray-400 uppercase mb-4">A Glimpse of Our Space</p>
          <h2 class="text-5xl font-extralight text-gray-800 tracking-[0.15em] uppercase mb-6">Gallery</h2>
          <div class="flex items-center justify-center gap-4">
            <div class="h-px w-16 bg-gray-200"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-green-700"></div>
            <div class="h-px w-16 bg-gray-200"></div>
          </div>
        </div>

        <!-- No Albums -->
        <div v-if="galleryAlbums.length === 0" class="text-center py-16 text-gray-400 font-light tracking-wider">
          No gallery photos available yet.
        </div>

        <!-- Album Stat Cards -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="album in galleryAlbums"
            :key="album.id"
            @click="galleryOpenAlbum = album"
            class="gallery-album-card group relative rounded-2xl overflow-hidden cursor-pointer shadow-md border border-gray-100 bg-white"
          >
            <!-- Cover image -->
            <div class="relative h-56 overflow-hidden bg-gray-200">
              <img
                v-if="album.photos && album.photos.length"
                :src="publicPhotoUrl(album.photos[0].filename)"
                :alt="album.name"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                @error="$event.target.src='/placeholder-image.jpg'"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <!-- Dark gradient overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent transition-opacity duration-300 group-hover:from-black/80"></div>
              <!-- Photo count badge -->
              <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-gray-700 text-[11px] font-medium tracking-wider px-2.5 py-1 rounded-full shadow-sm">
                {{ album.photos?.length ?? 0 }} photo{{ (album.photos?.length ?? 0) === 1 ? '' : 's' }}
              </div>
              <!-- View Album CTA -->
              <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <span class="border border-white/80 text-white text-[11px] tracking-[0.25em] uppercase px-5 py-2 font-light backdrop-blur-sm bg-white/10 rounded">
                  View Album
                </span>
              </div>
            </div>
            <!-- Card footer -->
            <div class="px-5 py-4 transition-colors duration-300 group-hover:bg-green-50">
              <p class="text-sm font-semibold text-gray-800 tracking-wide truncate group-hover:text-green-800 transition-colors duration-300">{{ album.name }}</p>
              <p v-if="album.description" class="text-xs text-gray-400 mt-0.5 truncate">{{ album.description }}</p>
              <p v-else class="text-xs text-gray-400 mt-0.5 italic">Click to explore</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Album Photos Modal -->
    <Teleport to="body">
      <Transition name="gallery-modal">
        <div
          v-if="galleryOpenAlbum"
          class="fixed inset-0 z-[55] flex flex-col bg-white"
        >
          <!-- Modal header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white shadow-sm flex-shrink-0">
            <div class="flex items-center gap-3">
              <button
                @click="galleryOpenAlbum = null"
                class="p-2 rounded-full text-gray-500 hover:text-green-700 hover:bg-green-50 transition"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
              </button>
              <div>
                <p class="text-[10px] tracking-[0.35em] text-gray-400 uppercase">Gallery</p>
                <h3 class="text-lg font-light text-gray-800 tracking-widest uppercase">{{ galleryOpenAlbum.name }}</h3>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-xs text-gray-400 font-light">{{ galleryOpenAlbum.photos?.length ?? 0 }} photo{{ (galleryOpenAlbum.photos?.length ?? 0) === 1 ? '' : 's' }}</span>
              <button
                @click="galleryOpenAlbum = null"
                class="p-2 rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
          </div>
          <!-- Photos grid -->
          <div class="flex-1 overflow-y-auto p-6">
            <div v-if="!galleryOpenAlbum.photos || galleryOpenAlbum.photos.length === 0"
              class="flex flex-col items-center justify-center h-64 text-gray-400 font-light gap-3">
              <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <p class="text-sm italic">No photos in this album.</p>
            </div>
            <div v-else class="columns-2 sm:columns-3 lg:columns-4 gap-3 space-y-3">
              <div
                v-for="photo in galleryOpenAlbum.photos"
                :key="photo.id"
                class="break-inside-avoid overflow-hidden rounded-lg cursor-pointer group"
                @click="galleryLightboxPhoto = photo"
              >
                <img
                  :src="publicPhotoUrl(photo.filename)"
                  :alt="photo.caption || galleryOpenAlbum.name"
                  class="w-full object-cover transition-transform duration-500 group-hover:scale-105"
                  style="display:block"
                  @error="$event.target.src='/placeholder-image.jpg'"
                />
                <div v-if="photo.caption" class="bg-white/90 text-xs text-gray-600 font-light tracking-wider px-2 py-1.5 truncate">
                  {{ photo.caption }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Gallery Lightbox -->
    <div v-if="galleryLightboxPhoto"
      class="fixed inset-0 z-[60] flex items-center justify-center bg-black/85"
      @click.self="galleryLightboxPhoto = null">
      <button class="absolute top-5 right-5 text-white/70 hover:text-white transition"
        @click="galleryLightboxPhoto = null">
        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="max-w-4xl w-full mx-4">
        <img :src="publicPhotoUrl(galleryLightboxPhoto.filename)"
          class="max-h-[82vh] w-full object-contain rounded-lg shadow-2xl" />
        <p v-if="galleryLightboxPhoto.caption" class="text-center text-white/70 text-sm font-light tracking-wider mt-4">
          {{ galleryLightboxPhoto.caption }}
        </p>
      </div>
    </div>

    <!-- ── Terms & Conditions Section ──────────────────────────────────── -->
    <section id="terms" class="py-24 bg-white border-t border-gray-100">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Heading -->
        <div class="text-center mb-16">
          <p class="text-[11px] tracking-[0.45em] text-gray-400 uppercase mb-4">Legal &amp; Policy</p>
          <h2 class="text-4xl font-extralight text-gray-800 tracking-[0.15em] uppercase mb-6">Terms &amp; Conditions</h2>
          <div class="flex items-center justify-center gap-4">
            <div class="h-px w-16 bg-gray-200"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
            <div class="h-px w-16 bg-gray-200"></div>
          </div>
        </div>

        <div class="space-y-10 text-gray-600 font-light leading-loose">

          <div>
            <h3 class="text-base font-semibold text-gray-800 tracking-widest uppercase mb-3 flex items-center gap-3">
              <span class="w-6 h-6 rounded-full bg-green-700 text-white text-xs flex items-center justify-center font-medium flex-shrink-0">1</span>
              Reservations &amp; Booking
            </h3>
            <ul class="pl-9 space-y-2 text-sm">
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>All reservations are subject to availability and confirmed only upon receipt of the required down payment.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>A valid government-issued ID must be presented upon check-in. The name on the ID must match the reservation.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Management reserves the right to cancel a booking if verification requirements are not met.</li>
            </ul>
          </div>

          <div>
            <h3 class="text-base font-semibold text-gray-800 tracking-widest uppercase mb-3 flex items-center gap-3">
              <span class="w-6 h-6 rounded-full bg-green-700 text-white text-xs flex items-center justify-center font-medium flex-shrink-0">2</span>
              Check-in &amp; Check-out
            </h3>
            <ul class="pl-9 space-y-2 text-sm">
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Standard check-in time is <strong>2:00 PM</strong>. Standard check-out time is <strong>12:00 Noon</strong>.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Early check-in and late check-out are subject to availability and may incur additional fees.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Guests are expected to vacate the premises promptly at the agreed check-out time.</li>
            </ul>
          </div>

          <div>
            <h3 class="text-base font-semibold text-gray-800 tracking-widest uppercase mb-3 flex items-center gap-3">
              <span class="w-6 h-6 rounded-full bg-green-700 text-white text-xs flex items-center justify-center font-medium flex-shrink-0">3</span>
              Cancellation &amp; Refund Policy
            </h3>
            <ul class="pl-9 space-y-2 text-sm">
              <li class="flex gap-2"><span class="text-amber-500 mt-1 flex-shrink-0">&#8212;</span>Cancellations made <strong>48 hours or more</strong> before the check-in date are eligible for a full refund of the down payment.</li>
              <li class="flex gap-2"><span class="text-amber-500 mt-1 flex-shrink-0">&#8212;</span>Cancellations made <strong>within 24 hours</strong> of check-in are non-refundable.</li>
              <li class="flex gap-2"><span class="text-amber-500 mt-1 flex-shrink-0">&#8212;</span>No-shows will be charged the full amount of the first night's stay.</li>
            </ul>
          </div>

          <div>
            <h3 class="text-base font-semibold text-gray-800 tracking-widest uppercase mb-3 flex items-center gap-3">
              <span class="w-6 h-6 rounded-full bg-green-700 text-white text-xs flex items-center justify-center font-medium flex-shrink-0">4</span>
              House Rules
            </h3>
            <ul class="pl-9 space-y-2 text-sm">
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Quiet hours are observed from <strong>10:00 PM to 7:00 AM</strong>. Noise disturbances will not be tolerated.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Smoking is strictly prohibited inside all rooms and common areas.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Pets are not allowed on the premises.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Any damage to property caused by guests will be billed accordingly.</li>
            </ul>
          </div>

          <div>
            <h3 class="text-base font-semibold text-gray-800 tracking-widest uppercase mb-3 flex items-center gap-3">
              <span class="w-6 h-6 rounded-full bg-green-700 text-white text-xs flex items-center justify-center font-medium flex-shrink-0">5</span>
              Data Privacy
            </h3>
            <ul class="pl-9 space-y-2 text-sm">
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>All personal information collected during the reservation process is used solely for booking confirmation and ID verification.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>We comply with the Philippines Data Privacy Act of 2012 (R.A. 10173). Your data will not be shared with third parties without your consent.</li>
            </ul>
          </div>

          <div>
            <h3 class="text-base font-semibold text-gray-800 tracking-widest uppercase mb-3 flex items-center gap-3">
              <span class="w-6 h-6 rounded-full bg-green-700 text-white text-xs flex items-center justify-center font-medium flex-shrink-0">6</span>
              Events &amp; Functions
            </h3>
            <ul class="pl-9 space-y-2 text-sm">
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Event bookings require a down payment to confirm the date. The balance is due on or before the event date.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>Cancellations for events must be made at least <strong>72 hours</strong> in advance.</li>
              <li class="flex gap-2"><span class="text-green-600 mt-1 flex-shrink-0">&#8212;</span>The management is not liable for damages or losses resulting from force majeure or events beyond our control.</li>
            </ul>
          </div>

        </div>

        <div class="mt-14 p-6 bg-green-50 border border-green-100 rounded-xl text-sm text-gray-600 font-light leading-loose">
          <p>By making a reservation or using the facilities of <strong class="text-green-800">Joanna's Nook Bed &amp; Breakfast</strong>, you acknowledge that you have read, understood, and agreed to these Terms &amp; Conditions. For inquiries, please contact us at <span class="text-green-700">{{ contactInfo.hotel_email || 'joannasnookb@gmail.com' }}</span> or call <span class="text-green-700">{{ contactInfo.hotel_phone || '' }}</span>.</p>
        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-28 bg-white overflow-hidden">
      <div class="max-w-5xl mx-auto px-4">

        <!-- Section Title -->
        <div class="text-center mb-24 svc-reveal svc-reveal--up" style="--svc-delay:0ms">
          <p class="text-lg tracking-[0.45em] text-gray-400 uppercase mb-5">What We Offer</p>
          <h2 class="text-5xl font-extralight text-gray-800 tracking-[0.15em] uppercase mb-6">Our Services</h2>
          <div class="flex items-center justify-center gap-4">
            <div class="h-px w-16 bg-gray-200"></div>
            <div class="w-1.5 h-1.5 rounded-full bg-green-700"></div>
            <div class="h-px w-16 bg-gray-200"></div>
          </div>
        </div>

        <!-- ── Room Reservations ── -->
        <div class="mb-24">
          <div class="text-center mb-12 svc-reveal svc-reveal--up" style="--svc-delay:0ms">
            <p class="text-base tracking-[0.4em] text-gray-400 uppercase mb-3">Accommodations</p>
            <h2 class="text-4xl font-extralight text-green-800 tracking-wider">Room Reservations</h2>
            <p class="text-lg text-gray-400 font-light mt-3 tracking-wider">Book comfortable accommodations for your stay with modern amenities</p>
            <div class="flex items-center justify-center gap-3 mt-5">
              <div class="h-px w-10 bg-gray-200"></div>
              <div class="w-1 h-1 rounded-full bg-amber-400"></div>
              <div class="h-px w-10 bg-gray-200"></div>
            </div>
          </div>

          <!-- Room Carousel Wrapper -->
          <div v-if="roomGroups.length">
            <!-- Single Room Card -->
            <div class="bg-white border border-gray-100 shadow-xl overflow-hidden flex flex-col sm:flex-row">
              <!-- Service Image -->
              <div class="relative w-full sm:w-80 flex-shrink-0 bg-gray-100 overflow-hidden svc-reveal svc-reveal--left svc-card-img-wrap" style="--svc-delay:120ms">
                <img :src="roomGroups[serviceRoomIndex].images[0]"
                  :alt="roomGroups[serviceRoomIndex].name"
                  class="w-full h-full object-cover" style="min-height:300px"
                  @error="$event.target.src = ROOM_PLACEHOLDER"/>
                <div class="absolute top-0 left-0 bg-black/60 text-white text-[9px] font-light px-3 py-1.5 uppercase tracking-[0.25em]">
                  {{ roomGroups[serviceRoomIndex].label }}
                </div>
                <!-- Availability badge -->
                <div class="absolute top-0 right-0 m-2">
                  <span class="text-xs font-medium px-2 py-1 rounded-full"
                    :class="roomGroups[serviceRoomIndex].availableCount > 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white'">
                    {{ roomGroups[serviceRoomIndex].availableCount }} / {{ roomGroups[serviceRoomIndex].roomList.length }} available
                  </span>
                </div>
              </div>
              <!-- Info -->
              <div class="flex-1 p-11 flex flex-col justify-between svc-reveal svc-reveal--right" style="--svc-delay:240ms">
                <div>
                  <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-light tracking-[0.18em] text-amber-700 uppercase">{{ roomGroups[serviceRoomIndex].name }}</h3>
                    <span class="text-sm text-gray-400 font-light tracking-widest">{{ serviceRoomIndex + 1 }} / {{ roomGroups.length }}</span>
                  </div>
                  <div class="h-px bg-gray-100 mb-5"></div>
                  <div class="flex items-center gap-4 mb-5">
                    <span class="text-3xl font-light text-amber-600">&#x20B1;{{ roomGroups[serviceRoomIndex].priceNum.toLocaleString() }}</span>
                    <span class="text-sm text-gray-400 flex items-center gap-1 tracking-wider">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                      {{ roomGroups[serviceRoomIndex].guests }} guests
                    </span>
                  </div>
                  <p class="text-l text-gray-500 font-light mb-6 leading-loose">{{ roomGroups[serviceRoomIndex].description }}</p>
                  <p class="text-l font-light text-gray-400 tracking-[0.3em] uppercase mb-4">Room Features</p>
                  <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                    <span v-for="(f, i) in roomGroups[serviceRoomIndex].features" :key="i" class="text-base text-gray-600 font-light flex items-center gap-2">
                      <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      {{ f }}
                    </span>
                  </div>
                </div>
                <button @click="openBookingForm(roomGroups[serviceRoomIndex])"
                  :disabled="roomGroups[serviceRoomIndex].availableCount === 0"
                  class="mt-8 w-full border border-green-700 hover:bg-green-700 hover:text-white disabled:opacity-40 disabled:cursor-not-allowed text-green-800 text-xs font-light py-4 tracking-[0.2em] uppercase transition-all duration-300">
                  {{ roomGroups[serviceRoomIndex].availableCount > 0 ? 'Reserve This Room' : 'No Rooms Available' }}
                </button>
              </div>
            </div>

            <!-- Bottom Nav: Prev · Dots · Next -->
            <div class="flex items-center justify-center gap-4 mt-6 svc-reveal svc-reveal--up" style="--svc-delay:360ms">
              <button @click="serviceRoomIndex = (serviceRoomIndex - 1 + roomGroups.length) % roomGroups.length"
                class="w-9 h-9 border border-gray-200 flex items-center justify-center text-gray-400 hover:border-green-700 hover:text-green-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <div class="flex gap-2">
                <button v-for="(group, i) in roomGroups" :key="group.name" type="button"
                  @click="serviceRoomIndex = i"
                  :class="['h-1 transition-all duration-300', i === serviceRoomIndex ? 'bg-green-700 w-8' : 'bg-gray-200 w-4 hover:bg-green-400']">
                </button>
              </div>
              <button @click="serviceRoomIndex = (serviceRoomIndex + 1) % roomGroups.length"
                class="w-9 h-9 border border-gray-200 flex items-center justify-center text-gray-400 hover:border-green-700 hover:text-green-700 transition">
                <svg class="w-.5 h-.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- ── Event Reservations ── -->
        <div class="mb-24">
          <div class="text-center mb-12 svc-reveal svc-reveal--up" style="--svc-delay:0ms">
            <p class="text-base tracking-[0.4em] text-gray-400 uppercase mb-3">Celebrations &amp; Gatherings</p>
            <h2 class="text-4xl font-extralight text-green-800 tracking-wider">Event Reservations</h2>
            <p class="text-lg text-gray-400 font-light mt-3 tracking-wider">Host your special events with our comprehensive event packages</p>
            <div class="flex items-center justify-center gap-3 mt-5">
              <div class="h-px w-10 bg-gray-200"></div>
              <div class="w-1 h-1 rounded-full bg-amber-400"></div>
              <div class="h-px w-10 bg-gray-200"></div>
            </div>
          </div>

          <!-- Package Carousel Wrapper -->
          <div v-if="eventPackages.length">
            <!-- Single Package Card -->
            <div class="bg-white border border-gray-100 shadow-xl overflow-hidden flex flex-col sm:flex-row">
              <!-- Service Image -->
              <div class="relative w-full sm:w-80 flex-shrink-0 bg-gray-100 overflow-hidden svc-reveal svc-reveal--left svc-card-img-wrap" style="--svc-delay:120ms">
                <img :src="eventPackages[serviceEventIndex].images[0]"
                  :alt="eventPackages[serviceEventIndex].name"
                  class="w-full h-full object-cover" style="min-height:300px"
                  @error="$event.target.src = '/Event%20Packages/Basic%20Package.jpg'"/>
              </div>
              <!-- Info -->
              <div class="flex-1 p-11 flex flex-col justify-between svc-reveal svc-reveal--right" style="--svc-delay:240ms">
                <div>
                  <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-light tracking-[0.18em] text-amber-700 uppercase">{{ eventPackages[serviceEventIndex].name }}</h3>
                    <span class="text-sm text-gray-400 font-light tracking-widest">{{ serviceEventIndex + 1 }} / {{ eventPackages.length }}</span>
                  </div>
                  <div class="h-px bg-gray-100 mb-5"></div>
                  <div class="flex items-center gap-4 mb-5">
                    <span class="text-3xl font-light text-amber-600">&#x20B1;{{ eventPackages[serviceEventIndex].priceNum.toLocaleString() }}</span>
                    <span class="text-sm text-gray-400 flex items-center gap-1 tracking-wider">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                      {{ eventPackages[serviceEventIndex].guests }} guests
                    </span>
                  </div>
                  <p class="text-base text-gray-500 font-light mb-6 leading-loose">{{ eventPackages[serviceEventIndex].description }}</p>
                  <p class="text-xs font-light text-gray-400 tracking-[0.3em] uppercase mb-4">Package Includes</p>
                  <ul class="space-y-3">
                    <li v-for="(inc, i) in eventPackages[serviceEventIndex].inclusions" :key="i" class="text-base text-gray-600 font-light flex items-center gap-2">
                      <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      {{ inc }}
                    </li>
                  </ul>
                </div>
                <button @click="openEventForm(eventPackages[serviceEventIndex].id)"
                  class="mt-8 w-full border border-purple-600 hover:bg-purple-600 hover:text-white text-purple-700 text-xs font-light py-4 tracking-[0.2em] uppercase transition-all duration-300">
                  Reserve This Package
                </button>
              </div>
            </div>

            <!-- Bottom Nav: Prev · Dots · Next -->
            <div class="flex items-center justify-center gap-4 mt-6 svc-reveal svc-reveal--up" style="--svc-delay:360ms">
              <button @click="serviceEventIndex = (serviceEventIndex - 1 + eventPackages.length) % eventPackages.length"
                class="w-9 h-9 border border-gray-200 flex items-center justify-center text-gray-400 hover:border-purple-600 hover:text-purple-600 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <div class="flex gap-2">
                <button v-for="(pkg, i) in eventPackages" :key="pkg.id" type="button"
                  @click="serviceEventIndex = i"
                  :class="['h-1 transition-all duration-300', i === serviceEventIndex ? 'bg-purple-500 w-8' : 'bg-gray-200 w-4 hover:bg-purple-300']">
                </button>
              </div>
              <button @click="serviceEventIndex = (serviceEventIndex + 1) % eventPackages.length"
                class="w-9 h-9 border border-gray-200 flex items-center justify-center text-gray-400 hover:border-purple-600 hover:text-purple-600 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- ── Why Choose Joanna's Nook ── -->
        <div class="mb-4 py-20 border-t border-gray-100">
          <div class="text-center mb-16 svc-reveal svc-reveal--up" style="--svc-delay:0ms">
            <p class="text-xs tracking-[0.4em] text-gray-400 uppercase mb-4">Our Promise</p>
            <h2 class="text-4xl font-extralight text-gray-800 tracking-wider">Why Choose Joanna's Nook?</h2>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-12">
            <div class="text-center svc-feature-card" style="--svc-delay:0ms">
              <div class="w-20 h-20 mx-auto mb-6 border border-green-100 flex items-center justify-center svc-feature-icon">
                <svg class="w-9 h-9 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              </div>
              <p class="text-xs tracking-[0.3em] text-amber-600 uppercase mb-3">Quality Accommodations</p>
              <p class="text-base text-gray-400 font-light leading-loose">Clean, comfortable rooms with modern amenities to ensure a pleasant stay.</p>
            </div>
            <div class="text-center svc-feature-card" style="--svc-delay:150ms">
              <div class="w-20 h-20 mx-auto mb-6 border border-green-100 flex items-center justify-center svc-feature-icon">
                <svg class="w-9 h-9 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <p class="text-xs tracking-[0.3em] text-amber-600 uppercase mb-3">Affordable Rates</p>
              <p class="text-base text-gray-400 font-light leading-loose">Competitive pricing with flexible payment options to suit your budget.</p>
            </div>
            <div class="text-center svc-feature-card" style="--svc-delay:300ms">
              <div class="w-20 h-20 mx-auto mb-6 border border-green-100 flex items-center justify-center svc-feature-icon">
                <svg class="w-9 h-9 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
              </div>
              <p class="text-xs tracking-[0.3em] text-amber-600 uppercase mb-3">Excellent Service</p>
              <p class="text-base text-gray-400 font-light leading-loose">Dedicated staff ready to assist you with all your needs during your stay.</p>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- Booking Modal -->
    <div v-if="showBookingModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[92vh] overflow-hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
          <h3 class="text-base font-semibold text-gray-800">Room Reservation</h3>
          <button @click="showBookingModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Scrollable Body -->
        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

          <!-- Logo + Title -->
          <div class="text-center mb-2">
            <img src="/Joannaslogo.png" alt="Joanna's" class="h-18 w-18 mx-auto mb-2 rounded-xl object-contain" />
            <h4 class="text-lg font-semibold text-green-800">Room Reservation</h4>
            <p class="text-xs text-gray-500">Fill in the details below to reserve a room.</p>
          </div>

          <!-- Select Room -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Select Available Room <span class="text-red-500">*</span></label>
            <select v-model="selectedRoomId" @change="onRoomSelect" required
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
              <option value="" disabled>-- Select a room --</option>
              <option v-for="r in selectedRoomGroup ? selectedRoomGroup.roomList : []" :key="r.id" :value="r.id" :disabled="r.status !== 'available'">
                Room {{ r.roomNumber }} &mdash; {{ r.status === 'available' ? '✓ Available' : '✗ ' + r.status }}
              </option>
            </select>
            <!-- Availability summary -->
            <div v-if="selectedRoomGroup" class="flex gap-2 mt-2 flex-wrap">
              <span v-for="r in selectedRoomGroup.roomList" :key="r.id"
                class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                :class="r.status === 'available' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-500 border border-red-200'">
                <span class="w-1.5 h-1.5 rounded-full" :class="r.status === 'available' ? 'bg-green-500' : 'bg-red-400'"></span>
                Room {{ r.roomNumber }}
              </span>
            </div>
          </div>

          <!-- Room Preview Card -->
          <div v-if="selectedRoom" class="flex gap-3 bg-green-50 border border-green-100 rounded-xl p-3">
            <img :src="selectedRoom.images[0]" :alt="selectedRoom.name" class="w-16 h-16 rounded-lg object-cover flex-shrink-0" />
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-amber-700">{{ selectedRoom.name }}</p>
              <p class="text-xs text-gray-500 font-light leading-relaxed truncate">{{ selectedRoom.description }}</p>
              <div class="flex justify-between items-center mt-1">
                <span class="text-xs text-gray-500">Selected Package Price:</span>
                <span class="text-sm font-bold text-green-800">&#x20B1;{{ selectedRoom.priceNum.toLocaleString() }}</span>
              </div>
            </div>
          </div>

          <!-- Customer Info -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Customer Name <span class="text-red-500">*</span></label>
              <input v-model="bookingForm.name" type="text" required placeholder="Full name"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
              <input v-model="bookingForm.email" type="email" required placeholder="email@example.com"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Contact Number <span class="text-red-500">*</span></label>
              <input v-model="bookingForm.contact" type="tel" required placeholder="09XX XXX XXXX"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Address <span class="text-red-500">*</span></label>
              <input v-model="bookingForm.address" type="text" required placeholder="Street, City"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Select Country</label>
              <select v-model="bookingForm.country"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="">Select Country</option>
                <option>Philippines</option>
                <option>United States</option>
                <option>Japan</option>
                <option>South Korea</option>
                <option>Australia</option>
                <option>Singapore</option>
                <option>Other</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Additional Guests (Max: {{ selectedRoom?.guests || 0 }})</label>
              <input v-model="bookingForm.additionalGuests" type="number" min="0" :max="selectedRoom?.guests || 0" placeholder="0"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Check-In Date <span class="text-red-500">*</span></label>
              <input v-model="bookingForm.checkIn" type="date" required :min="todayDate"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Check-Out Date <span class="text-red-500">*</span></label>
              <input v-model="bookingForm.checkOut" type="date" required :min="bookingForm.checkIn || todayDate"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>

          <!-- ID Upload -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">ID Upload</label>
            <input type="file" accept="image/*,.pdf" @change="e => bookingForm.idFile = e.target.files[0]"
              class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-green-50 file:text-green-800 hover:file:bg-green-100" />
          </div>

          <!-- Additional Requests -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Additional Requests</label>
            <textarea v-model="bookingForm.requests" rows="3" placeholder="Any special requests..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
          </div>

          <!-- Estimated Total -->
          <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-3 flex justify-between items-center">
            <div>
              <p class="text-xs text-gray-500 font-medium">Estimated Total</p>
              <p class="text-xs text-gray-400">{{ computedNights }} Night{{ computedNights !== 1 ? 's' : '' }}</p>
            </div>
            <p class="text-xl font-bold text-amber-600">&#x20B1;{{ computedTotal.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</p>
          </div>

          <!-- Terms and Conditions -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Terms and Conditions <span class="text-red-500">*</span></label>
            <div ref="termsBoxRef" @scroll="handleTermsScroll" class="border border-gray-200 rounded-lg p-3 h-32 overflow-y-auto text-xs text-gray-600 leading-relaxed space-y-2 bg-gray-50">
              <p class="font-semibold text-gray-700">Valid ID Requirement</p>
              <ul class="list-disc pl-4 space-y-1">
                <li>A clear photo or scanned copy of a valid government-issued ID must be uploaded with your reservation request.</li>
                <li>The reservation name must match the name on the ID.</li>
                <li>If booking for another person, an authorization and their ID must be provided.</li>
              </ul>
              <p class="font-semibold text-gray-700">Verification &amp; Confirmation</p>
              <ul class="list-disc pl-4 space-y-1">
                <li>All reservation requests are subject to approval after ID verification.</li>
                <li>The management may contact you for additional requirements before confirming the booking.</li>
                <li>Check-in time is 2:00 PM. Check-out time is 12:00 PM.</li>
                <li>Late check-out may incur additional charges subject to room availability.</li>
              </ul>
              <p class="font-semibold text-gray-700">Cancellation Policy</p>
              <ul class="list-disc pl-4 space-y-1">
                <li>Cancellations made 48 hours before check-in are eligible for a full refund.</li>
                <li>Cancellations made within 24 hours are non-refundable.</li>
              </ul>
            </div>
            <p class="text-xs mt-1" :class="termsScrolled ? 'text-green-500' : 'text-red-400'">{{ termsScrolled ? '✓ You have read all the terms. You may now check the boxes below.' : 'Please scroll through and read all terms above to enable the checkboxes' }}</p>
          </div>

          <!-- Required Agreements -->
          <div class="space-y-2">
            <p class="text-xs font-medium text-gray-600">Required Agreements</p>
            <label class="flex items-start gap-2" :class="termsScrolled ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'">
              <input type="checkbox" v-model="agreedPrivacy" :disabled="!termsScrolled" class="mt-0.5 rounded accent-green-700 disabled:cursor-not-allowed" />
              <span class="text-xs text-gray-600">I have read and accept the <span class="text-amber-600 underline">Data Privacy &amp; Policy</span></span>
            </label>
            <label class="flex items-start gap-2" :class="termsScrolled ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'">
              <input type="checkbox" v-model="agreedTerms" :disabled="!termsScrolled" class="mt-0.5 rounded accent-green-700 disabled:cursor-not-allowed" />
              <span class="text-xs text-gray-600">I have read and agree to the <span class="text-amber-600 underline">Terms &amp; Conditions of Stay</span></span>
            </label>
          </div>

        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0">
          <!-- Success state -->
          <div v-if="bookingSuccessRef" class="flex flex-col items-center gap-2 py-1">
            <div class="flex items-center gap-2 text-green-600">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span class="font-semibold text-base text-gray-800">Reservation Submitted!</span>
            </div>
            <p class="text-sm text-gray-500 text-center">Your reference number: <span class="font-bold text-amber-600">{{ bookingSuccessRef }}</span></p>
            <p class="text-xs text-gray-400 text-center">We will contact you shortly to confirm your booking. Please note your reference number for tracking.</p>
            <button @click="showBookingModal = false; bookingSuccessRef = ''"
              class="mt-1 px-6 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition font-medium">Done</button>
          </div>
          <!-- Normal state -->
          <div v-else class="flex justify-end gap-3">
            <button @click="showBookingModal = false"
              class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button @click="submitBooking" :disabled="bookingSubmitting || !agreedPrivacy || !agreedTerms || !selectedRoomId || !bookingForm.checkIn || !bookingForm.checkOut"
              class="px-5 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
              <svg v-if="bookingSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
              {{ bookingSubmitting ? 'Submitting...' : 'Submit Reservation' }}
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Event Reservation Modal -->
    <div v-if="showEventModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[92vh] overflow-hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
          <h3 class="text-base font-semibold text-gray-800">Event Reservation</h3>
          <button @click="showEventModal = false" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Scrollable Body -->
        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

          <!-- Logo + Title -->
          <div class="text-center mb-1">
            <img src="/Joannaslogo.png" alt="Joanna's" class="h-18 w-18 mx-auto mb-2 rounded-xl object-contain" />
            <h4 class="text-lg font-semibold text-green-800">Event Reservation</h4>
            <p class="text-xs text-gray-500">Provide details for your event reservation and our team will contact you.</p>
          </div>

          <!-- Info box -->
          <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-xs text-green-800 leading-relaxed">
            <strong>Event Pricing:</strong> Prices are automatically calculated when you select an event package. Additional guests will be charged on per-person basis for your chosen food set.
          </div>

          <!-- Event Name + Type -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Event Name <span class="text-red-500">*</span></label>
              <input v-model="eventForm.eventName" type="text" required placeholder="e.g. Birthday Party"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Event Type</label>
              <select v-model="eventForm.eventType"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="">Select Type</option>
                <option>Birthday</option>
                <option>Wedding</option>
                <option>Debut</option>
                <option>Anniversary</option>
                <option>Corporate Event</option>
                <option>Reunion</option>
                <option>Other</option>
              </select>
            </div>
          </div>

          <!-- Customer Name + Email -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Customer Name <span class="text-red-500">*</span></label>
              <input v-model="eventForm.name" type="text" required placeholder="Full name"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Customer Email <span class="text-red-500">*</span></label>
              <input v-model="eventForm.email" type="email" required placeholder="email@example.com"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>

          <!-- Contact + Address -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Contact Number <span class="text-red-500">*</span></label>
              <input v-model="eventForm.contact" type="tel" required placeholder="09XX XXX XXXX"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Address <span class="text-red-500">*</span></label>
              <input v-model="eventForm.address" type="text" required placeholder="Street, City"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>

          <!-- Supervisor + Date -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Select Supervisor</label>
              <select v-model="eventForm.supervisor"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="">Select Supervisor</option>
                <option>Supervisor A</option>
                <option>Supervisor B</option>
                <option>Supervisor C</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Event Date <span class="text-red-500">*</span></label>
              <input v-model="eventForm.eventDate" type="date" required :min="todayDate"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>

          <!-- From Time + Additional Guests -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">From Time</label>
              <select v-model="eventForm.fromTime"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                <option value="">Select Time</option>
                <option v-for="t in timeSlots" :key="t" :value="t">{{ t }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Additional Guests <span class="text-red-500">*</span></label>
              <input v-model="eventForm.guestCount" type="number" min="1" required placeholder="0"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400" />
            </div>
          </div>

          <!-- Select Package Carousel -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-2">Select Package <span class="text-gray-400 font-normal">(optional)</span></label>
            <div v-if="eventPackages.length">
              <div class="flex items-center gap-2">
                <!-- Prev -->
                <button type="button"
                  @click="pkgCarouselIndex = Math.max(0, pkgCarouselIndex - 1)"
                  :disabled="pkgCarouselIndex === 0"
                  class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 bg-white shadow-sm text-gray-500 disabled:opacity-30 hover:border-green-400 hover:text-amber-600 transition text-base">
                  &#8592;
                </button>
                <!-- Card -->
                <div class="flex-1 border rounded-xl p-4 cursor-pointer transition-all text-center"
                  @click="eventForm.selectedPackageId = eventPackages[pkgCarouselIndex].id; eventForm.selectedPackage = eventPackages[pkgCarouselIndex]"
                  :class="eventForm.selectedPackageId === eventPackages[pkgCarouselIndex].id ? 'border-blue-500 bg-green-50 ring-2 ring-blue-300' : 'border-gray-200 hover:border-green-300 bg-white'">
                  <div v-if="eventForm.selectedPackageId === eventPackages[pkgCarouselIndex].id" class="flex justify-center mb-1.5">
                    <span class="text-xs bg-green-600 text-white px-2 py-0.5 rounded-full font-medium">&#10003; Selected</span>
                  </div>
                  <p class="text-sm font-bold text-gray-800">{{ eventPackages[pkgCarouselIndex].name }}</p>
                  <p class="text-xl font-extrabold text-green-800 mt-1">&#x20B1;{{ eventPackages[pkgCarouselIndex].priceNum.toLocaleString() }}.00</p>
                  <p v-if="eventPackages[pkgCarouselIndex].description" class="text-xs text-gray-500 mt-1 line-clamp-2">{{ eventPackages[pkgCarouselIndex].description }}</p>
                  <p class="text-xs text-gray-400 mt-1.5">Up to {{ eventPackages[pkgCarouselIndex].guests }} guests</p>
                </div>
                <!-- Next -->
                <button type="button"
                  @click="pkgCarouselIndex = Math.min(eventPackages.length - 1, pkgCarouselIndex + 1)"
                  :disabled="pkgCarouselIndex === eventPackages.length - 1"
                  class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 bg-white shadow-sm text-gray-500 disabled:opacity-30 hover:border-green-400 hover:text-amber-600 transition text-base">
                  &#8594;
                </button>
              </div>
              <!-- Dots + Counter -->
              <div class="flex justify-center items-center gap-1.5 mt-3">
                <button v-for="(pkg, i) in eventPackages" :key="pkg.id" type="button"
                  @click="pkgCarouselIndex = i"
                  :class="['h-2 rounded-full transition-all duration-300', i === pkgCarouselIndex ? 'bg-green-600 w-5' : (eventForm.selectedPackageId === pkg.id ? 'bg-green-400 w-2' : 'bg-gray-300 w-2')]">
                </button>
              </div>
              <p class="text-center text-xs text-gray-400 mt-1">{{ pkgCarouselIndex + 1 }} / {{ eventPackages.length }}</p>
            </div>
            <div v-else class="text-xs text-gray-400 text-center py-4 border border-dashed border-gray-200 rounded-xl">Loading packages...</div>
          </div>

          <!-- Buffet Set -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-2">Buffet Set <span class="text-gray-400 font-normal">(optional)</span></label>
            <div class="grid grid-cols-4 gap-2">
              <div v-for="s in buffetSets" :key="s.id"
                @click="eventForm.selectedSetId = s.id; eventForm.selectedSet = s; eventForm.selectedFoods = []"
                :class="['border rounded-xl p-2 cursor-pointer transition-all text-center', eventForm.selectedSetId === s.id ? 'border-blue-500 bg-green-50 ring-2 ring-blue-300' : 'border-gray-200 hover:border-green-300']">
                <p class="text-xs font-semibold text-gray-700">{{ s.label }}</p>
                <p class="text-xs text-amber-600 font-medium mt-0.5">&#x20B1;{{ s.price.toLocaleString() }}</p>
                <p class="text-xs text-gray-400">{{ s.desc }}</p>
              </div>
            </div>

            <!-- Food Picker -->
            <div v-if="eventForm.selectedSet" class="mt-3 border border-green-100 rounded-xl bg-green-50/50 p-4">
              <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-green-800">Select your dishes for {{ eventForm.selectedSet.label }}</p>
                <span :class="['text-xs font-bold px-2 py-0.5 rounded-full', eventForm.selectedFoods.length > eventForm.selectedSet.desc.match(/\d+/)[0] ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-800']">
                  {{ eventForm.selectedFoods.length }} / {{ eventForm.selectedSet.desc.match(/\d+/)[0] }} selected
                </span>
              </div>
              <div v-for="cat in eventForm.selectedSet.foods" :key="cat.category" class="mb-3">
                <p class="text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">{{ cat.category }}</p>
                <div class="grid grid-cols-2 gap-1">
                  <label v-for="item in cat.items" :key="item"
                    class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" :value="item" v-model="eventForm.selectedFoods"
                      :disabled="!eventForm.selectedFoods.includes(item) && eventForm.selectedFoods.length >= Number(eventForm.selectedSet.desc.match(/\d+/)[0])"
                      class="w-3.5 h-3.5 rounded accent-green-700 disabled:opacity-40" />
                    <span :class="['text-xs', eventForm.selectedFoods.includes(item) ? 'text-green-800 font-medium' : 'text-gray-600 group-hover:text-gray-800']">{{ item }}</span>
                  </label>
                </div>
              </div>
              <p v-if="eventForm.selectedFoods.length >= Number(eventForm.selectedSet.desc.match(/\d+/)[0])" class="text-xs text-orange-500 mt-1">
                Maximum dishes reached. Deselect a dish to choose a different one.
              </p>
            </div>
          </div>

          <!-- Additional Requests -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Additional Requests</label>
            <textarea v-model="eventForm.requests" rows="3" placeholder="Special arrangements, dietary needs, decorations..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
          </div>

          <!-- Remarks -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
            <textarea v-model="eventForm.remarks" rows="2" placeholder="Any other remarks..."
              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-400"></textarea>
          </div>

          <!-- Customer ID + Estimated Total -->
          <div class="grid grid-cols-2 gap-3 items-end">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Customer ID</label>
              <input type="file" accept="image/*,.pdf" @change="e => eventForm.idFile = e.target.files[0]"
                class="w-full text-sm text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-green-50 file:text-green-800 hover:file:bg-green-100" />
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-3 text-right">
              <p class="text-xs text-gray-500">Estimated Total</p>
              <p class="text-xl font-bold text-green-800">&#x20B1;{{ eventComputedTotal.toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</p>
            </div>
          </div>

          <!-- Terms and Conditions -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Terms and Conditions <span class="text-red-500">*</span></label>
            <div ref="eventTermsBoxRef" @scroll="handleEventTermsScroll" class="border border-gray-200 rounded-lg p-3 h-32 overflow-y-auto text-xs text-gray-600 leading-relaxed space-y-2 bg-gray-50">
              <p class="font-semibold text-gray-700">1. Valid ID Identification Required</p>
              <p>All guests are required to present a valid government-issued ID (such as Driver's License, Passport, National ID, or any government-recognized identification) upon check-in. The uploaded ID will be used for reservation verification and must match the guest information provided in the reservation.</p>
              <p class="font-semibold text-gray-700">2. Identity Verification</p>
              <p>The management reserves the right to verify the authenticity of the submitted identification documents. Failure to provide valid identification or discrepancies in the provided information may result in cancellation of reservation without refund.</p>
              <p class="font-semibold text-gray-700">3. Check-in Verification</p>
              <p>All event reservations are subject to confirmation by the management. The event date and time are subject to venue availability. Cancellations must be made 72 hours before the event date.</p>
            </div>
            <p class="text-xs mt-1" :class="eventTermsScrolled ? 'text-green-500' : 'text-red-400'">{{ eventTermsScrolled ? '✓ You have read all the terms. You may now check the boxes below.' : 'Please scroll through and read all Terms and Conditions regarding ID verification and data privacy.' }}</p>
          </div>

          <!-- Required Agreements -->
          <div class="space-y-2">
            <p class="text-xs font-medium text-gray-600">Required Agreements <span class="text-red-500">*</span></p>
            <label class="flex items-start gap-2" :class="eventTermsScrolled ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'">
              <input type="checkbox" v-model="eventAgreedPrivacy" :disabled="!eventTermsScrolled" class="mt-0.5 rounded accent-green-700 disabled:cursor-not-allowed" />
              <span class="text-xs text-gray-600">I have read and agreed to the <span class="text-amber-600 underline">Data Privacy &amp; Policy</span></span>
            </label>
            <label class="flex items-start gap-2" :class="eventTermsScrolled ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'">
              <input type="checkbox" v-model="eventAgreedTerms" :disabled="!eventTermsScrolled" class="mt-0.5 rounded accent-green-700 disabled:cursor-not-allowed" />
              <span class="text-xs text-gray-600">I have read and agree to the <span class="text-amber-600 underline">Terms &amp; Conditions</span></span>
            </label>
          </div>

        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-100 flex-shrink-0">
          <!-- Success state -->
          <div v-if="eventSuccessRef" class="flex flex-col items-center gap-2 py-1">
            <div class="flex items-center gap-2 text-green-600">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span class="font-semibold text-base text-gray-800">Event Request Submitted!</span>
            </div>
            <p class="text-sm text-gray-500 text-center">Your reference number: <span class="font-bold text-green-800">{{ eventSuccessRef }}</span></p>
            <p class="text-xs text-gray-400 text-center">Our events team will contact you within 24 hours to confirm the details.</p>
            <button @click="showEventModal = false; eventSuccessRef = ''"
              class="mt-1 px-6 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition font-medium">Done</button>
          </div>
          <!-- Normal state -->
          <div v-else class="flex justify-end gap-3">
            <button @click="showEventModal = false"
              class="px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">Cancel</button>
            <button @click="submitEventInquiry" :disabled="eventSubmitting || !eventAgreedPrivacy || !eventAgreedTerms || !eventForm.name || !eventForm.eventDate"
              class="px-5 py-2 text-sm bg-green-800 text-white rounded-xl hover:bg-green-900 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
              <svg v-if="eventSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
              {{ eventSubmitting ? 'Submitting...' : 'Submit Reservation' }}
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- User Manual is in the floating side drawer below -->

    <!-- Find Us / Contact Section -->
    <section id="contact" class="py-24 bg-white border-t border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-14">
          <p class="text-[11px] tracking-[0.4em] text-gray-400 uppercase mb-3">Visit Us</p>
          <h2 class="text-3xl font-extralight text-green-800 tracking-wider">Find Us</h2>
          <div class="flex items-center gap-4 mt-4">
            <div class="h-px w-16 bg-gray-200"></div>
            <div class="w-1 h-1 rounded-full bg-amber-400"></div>
          </div>
        </div>
        <div class="flex flex-col lg:flex-row gap-6">

          <!-- Map -->
          <div class="flex-1 min-h-72 overflow-hidden shadow-lg border border-gray-100" style="min-height:380px;isolation:isolate;position:relative;z-index:0">
            <div ref="mapRef" style="width:100%;height:100%;min-height:380px"></div>
          </div>

          <!-- Right column -->
          <div class="w-full lg:w-80 flex flex-col gap-4">

            <!-- Contact Information -->
            <div class="bg-white border border-gray-100 shadow-lg p-7">
              <p class="text-[11px] tracking-[0.35em] text-gray-400 uppercase mb-1">Get in Touch</p>
              <h3 class="text-base font-light text-amber-600 tracking-wider mb-5">Contact Information</h3>
              <div class="space-y-4">
                <!-- Address -->
                <div class="flex gap-3">
                  <div class="w-8 h-8 border border-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-[11px] tracking-[0.3em] text-amber-600 uppercase mb-1">Address</p>
                    <p class="text-sm text-gray-700 mb-1.5">{{ contactInfo.hotel_address || 'Balingasag, Misamis Oriental' }}</p>
                    <a href="https://www.google.com/maps?q=8.74436558174729,124.781658936795" target="_blank"
                      class="text-[10px] text-green-600 hover:text-green-800 flex items-center gap-1 tracking-wider transition">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                      Get Directions
                    </a>
                  </div>
                </div>
                <div class="h-px bg-gray-100"></div>
                <!-- Phone -->
                <div class="flex gap-3">
                  <div class="w-8 h-8 border border-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-[11px] tracking-[0.3em] text-amber-600 uppercase mb-1">Phone</p>
                    <p class="text-sm text-gray-700">{{ contactInfo.hotel_phone || 'N/A' }}</p>
                  </div>
                </div>
                <div class="h-px bg-gray-100"></div>
                <!-- Email -->
                <div class="flex gap-3">
                  <div class="w-8 h-8 border border-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-[11px] tracking-[0.3em] text-amber-600 uppercase mb-1">Email</p>
                    <p class="text-sm text-gray-700">{{ contactInfo.hotel_email || 'N/A' }}</p>
                  </div>
                </div>
                <div class="h-px bg-gray-100"></div>
                <!-- Business Hours -->
                <div class="flex gap-3">
                  <div class="w-8 h-8 border border-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-[11px] tracking-[0.3em] text-amber-600 uppercase mb-1">Business Hours</p>
                    <p class="text-sm text-gray-700">Monday &ndash; Sunday: 24/7</p>
                    <p class="text-sm text-gray-400 font-light">Front Desk Available Round the Clock</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-green-50 border border-green-100 shadow p-6">
              <h3 class="text-[11px] tracking-[0.35em] text-gray-400 uppercase mb-4">Quick Links</h3>
              <ul class="space-y-2">
                <li>
                  <a href="#services" class="flex items-center gap-2 text-sm text-green-800 hover:text-green-900 tracking-wider transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
                    View Our Services
                  </a>
                </li>
                <li>
                  <button @click="showBookingModal = true" class="flex items-center gap-2 text-sm text-green-800 hover:text-green-900 tracking-wider transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
                    Book a Room
                  </button>
                </li>
                <li>
                  <button @click="showEventModal = true" class="flex items-center gap-2 text-sm text-green-800 hover:text-green-900 tracking-wider transition">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
                    Book an Event
                  </button>
                </li>
              </ul>
            </div>

            <!-- Follow Us -->
            <div class="bg-white border border-gray-100 shadow p-6">
              <h3 class="text-[11px] tracking-[0.35em] text-gray-400 uppercase mb-4">Follow Us</h3>
              <div class="flex gap-3">
                <a href="https://www.facebook.com/joannasnook.inquire" target="_blank" rel="noopener noreferrer"
                  class="w-10 h-10 rounded-full bg-green-700 flex items-center justify-center hover:bg-green-800 transition shadow">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                  </svg>
                </a>
                <a href="https://www.instagram.com/joannasnookofficial/" target="_blank" rel="noopener noreferrer"
                  class="w-10 h-10 rounded-full flex items-center justify-center hover:opacity-90 transition shadow"
                  style="background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%)">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                    <circle cx="12" cy="12" r="4"/>
                    <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                  </svg>
                </a>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="relative bg-gray-950 text-gray-400 overflow-hidden">
      <!-- Decorative top accent line -->
      <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-green-500 to-transparent opacity-60"></div>

      <!-- Main grid -->
      <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-20">

          <!-- About -->
          <div>
            <div class="flex items-center gap-2 mb-6">
              <span class="block w-5 h-px bg-green-500"></span>
              <p class="text-[10px] tracking-[0.4em] text-green-500/80 uppercase font-semibold">About</p>
            </div>
            <p class="text-sm font-light leading-relaxed text-gray-500">
              Your premier destination for warm hospitality and unforgettable experiences nestled in the heart of Balingasag.
            </p>
            <div class="mt-8">
              <div class="flex items-center gap-2 mb-6">
                <span class="block w-5 h-px bg-green-500"></span>
                <p class="text-[10px] tracking-[0.4em] text-green-500/80 uppercase font-semibold">Contact</p>
              </div>
              <ul class="space-y-3 text-sm font-light text-gray-500">
                <li class="flex items-start gap-2">
                  <svg class="w-4 h-4 mt-0.5 text-green-600/60 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                  <span>{{ contactInfo.hotel_phone || 'N/A' }}</span>
                </li>
                <li class="flex items-start gap-2">
                  <svg class="w-4 h-4 mt-0.5 text-green-600/60 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                  <span>{{ contactInfo.hotel_email || 'N/A' }}</span>
                </li>
                <li class="flex items-start gap-2">
                  <svg class="w-4 h-4 mt-0.5 text-green-600/60 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                  <span>{{ contactInfo.hotel_address || '' }}</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Navigation -->
          <div>
            <div class="flex items-center gap-2 mb-6">
              <span class="block w-5 h-px bg-green-500"></span>
              <p class="text-[10px] tracking-[0.4em] text-green-500/80 uppercase font-semibold">Navigation</p>
            </div>
            <ul class="space-y-4">
              <li v-for="link in [
                { href: '#home', label: 'Home' },
                { href: '#about', label: 'About Us' },
                { href: '#gallery', label: 'Gallery' },
                { href: '#terms', label: 'Terms & Conditions' },
                { href: '#services', label: 'Services' },
                { href: '#contact', label: 'Contact Us' }
              ]" :key="link.href">
                <a :href="link.href"
                   class="group flex items-center gap-3 text-sm font-light text-gray-500 hover:text-white transition-colors duration-200">
                  <span class="block w-0 group-hover:w-4 h-px bg-green-500 transition-all duration-300"></span>
                  {{ link.label }}
                </a>
              </li>
            </ul>
          </div>

          <!-- Developer credit -->
          <div class="flex flex-col">
            <div class="flex items-center gap-2 mb-6">
              <span class="block w-5 h-px bg-green-500"></span>
              <p class="text-[10px] tracking-[0.4em] text-green-500/80 uppercase font-semibold">Developer</p>
            </div>
            <div class="flex-1 flex flex-col items-start gap-4">
              <img
                src="/JSM Digital Logo.png"
                alt="JSM Digital"
                class="w-14 h-14 object-cover rounded-full border border-gray-700 opacity-80 hover:opacity-100 transition-opacity duration-300"
                title="JSM Digital"
              />
              <div>
                <p class="text-white text-sm font-light tracking-widest mb-0.5">James Stanley Macarulay</p>
                <p class="text-[11px] text-gray-500 tracking-[0.25em]">from JSM Digital</p>
              </div>
              <p class="text-xs text-gray-600 font-light leading-relaxed mt-1">
                Crafted with care &mdash; delivering modern digital solutions for hospitality businesses.
              </p>
            </div>
          </div>

        </div>
      </div>

      <!-- Bottom bar -->
      <div class="border-t border-gray-800/60 py-6 px-6">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
          <p class="text-[11px] font-light tracking-widest text-gray-600 uppercase">
            © 2026 Joanna's Nook Bed &amp; Breakfast &mdash; All Rights Reserved
          </p>
          <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-[10px] tracking-widest text-gray-600 uppercase">Open for Reservations</span>
          </div>
        </div>
      </div>
    </footer>
  </div>

  <!-- ── Floating ? Guest Manual Button ── -->
  <button
    @click="showGuestManual = !showGuestManual"
    class="fixed bottom-6 right-4 z-50 w-11 h-11 bg-green-700 hover:bg-green-800 text-white rounded-full shadow-xl flex items-center justify-center font-bold text-xl leading-none transition-all duration-200 hover:scale-110 select-none"
    title="How to Book — Guest Guide"
  >?</button>

  <!-- Guest Manual Modal -->
  <Transition name="gm-modal">
    <div
      v-if="showGuestManual"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
      @click.self="showGuestManual = false"
    >
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col" style="max-height:90vh">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332-.477-4.5-1.253"/>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-900">How to Book with Joanna's Nook</p>
              <p class="text-xs text-gray-400 font-light">Step-by-step guest booking guide</p>
            </div>
          </div>
          <button @click="showGuestManual = false" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

        <!-- PHASE A: Getting Started -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <div class="w-6 h-6 rounded-full bg-green-700 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">A</div>
            <h3 class="text-xs font-bold text-green-800 uppercase tracking-widest">Getting Started</h3>
            <div class="flex-1 h-px bg-green-100"></div>
          </div>
          <div class="space-y-2">

            <!-- Step 1 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-green-700 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">1</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-1">Visit the Services Page</p>
                <p class="text-xs text-gray-500 font-light leading-relaxed">Click <strong class="text-amber-600">Services</strong> in the nav bar or the <strong class="text-amber-600">&ldquo;View Our Services&rdquo;</strong> button to browse all rooms and event packages.</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-amber-600 bg-green-50 rounded-lg px-3 py-1.5">
                  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  No account needed to browse.
                </div>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-green-700 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">2</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-1">Choose a Service Type</p>
                <p class="text-xs text-gray-500 font-light leading-relaxed">Use the carousel arrows to browse available options.</p>
                <div class="mt-2 grid grid-cols-2 gap-1.5">
                  <div class="flex items-center gap-1.5 bg-green-50 border border-green-100 rounded-lg px-2 py-1.5 text-xs text-green-800 font-medium"><span>🏨</span> Room Stay</div>
                  <div class="flex items-center gap-1.5 bg-purple-50 border border-purple-100 rounded-lg px-2 py-1.5 text-xs text-purple-700 font-medium"><span>🎉</span> Event Package</div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- PHASE B: Making Your Reservation -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <div class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">B</div>
            <h3 class="text-xs font-bold text-indigo-700 uppercase tracking-widest">Making Your Reservation</h3>
            <div class="flex-1 h-px bg-indigo-100"></div>
          </div>
          <div class="space-y-2">

            <!-- Step 3 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">3</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-1">Click &ldquo;Reserve&rdquo;</p>
                <p class="text-xs text-gray-500 font-light leading-relaxed">Find a room or package you like and click <strong class="text-indigo-600">&ldquo;Reserve This Room&rdquo;</strong> or <strong class="text-purple-600">&ldquo;Reserve This Package&rdquo;</strong>. A booking form will appear.</p>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">4</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-1">Select Your Date</p>
                <p class="text-xs text-gray-500 font-light leading-relaxed">Pick your <strong class="text-indigo-600">check-in &amp; check-out</strong> dates (rooms) or <strong class="text-indigo-600">event date</strong> (packages).</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 rounded-lg px-3 py-1.5">
                  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  Past dates cannot be selected.
                </div>
              </div>
            </div>

            <!-- Step 5 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">5</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-1">Fill In Your Details</p>
                <p class="text-xs text-gray-500 font-light mb-2">Complete the booking form with:</p>
                <ul class="space-y-1">
                  <li class="flex items-center gap-1.5 text-xs text-gray-500"><svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Full name &amp; email</li>
                  <li class="flex items-center gap-1.5 text-xs text-gray-500"><svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Contact number &amp; address</li>
                  <li class="flex items-center gap-1.5 text-xs text-gray-500"><svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Number of guests</li>
                  <li class="flex items-center gap-1.5 text-xs text-gray-500"><svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Valid government-issued ID (photo or PDF)</li>
                </ul>
              </div>
            </div>

            <!-- Step 6 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">6</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-1">Read &amp; Agree to Terms</p>
                <p class="text-xs text-gray-500 font-light leading-relaxed">Scroll through the <strong class="text-indigo-600">Terms &amp; Conditions</strong> box. The checkboxes unlock once you've read everything — tick both to proceed.</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-red-600 bg-red-50 rounded-lg px-3 py-1.5">
                  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Checkboxes are locked until all terms are read.
                </div>
              </div>
            </div>

            <!-- Step 7 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">7</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-1">Submit Your Reservation</p>
                <p class="text-xs text-gray-500 font-light leading-relaxed">Click <strong class="text-indigo-600">&ldquo;Submit Reservation&rdquo;</strong>. You'll receive a unique <strong class="text-indigo-600">Reference Number</strong> — keep it to track your booking.</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-green-700 bg-green-50 rounded-lg px-3 py-1.5">
                  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Screenshot or write down your reference number.
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- PHASE C: Paying Your Down Payment -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <div class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">C</div>
            <h3 class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Paying Your Down Payment</h3>
            <div class="flex-1 h-px bg-emerald-100"></div>
          </div>
          <div class="space-y-2">

            <!-- Step 8 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">8</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-1">Wait for Down Payment Prompt</p>
                <p class="text-xs text-gray-500 font-light leading-relaxed">Once your reservation is <strong class="text-emerald-600">approved</strong>, you'll receive instructions to submit your down payment.</p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-700 bg-emerald-50 rounded-lg px-3 py-1.5">
                  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                  Down payment is required to secure your booking.
                </div>
              </div>
            </div>

            <!-- Step 9 -->
            <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">9</div>
              <div class="min-w-0">
                <p class="text-xs font-semibold text-gray-800 mb-2">Select a Payment Method</p>
                <div class="space-y-1.5">
                  <div class="flex items-center gap-2.5 bg-green-50 border border-green-100 rounded-lg px-3 py-2">
                    <span class="text-base leading-none">📱</span>
                    <div><p class="text-xs font-semibold text-amber-700">GCash</p><p class="text-xs text-amber-600 font-light">Send to our GCash number and upload the screenshot as proof.</p></div>
                  </div>
                  <div class="flex items-center gap-2.5 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2">
                    <span class="text-base leading-none">💵</span>
                    <div><p class="text-xs font-semibold text-emerald-800">Cash On-Site</p><p class="text-xs text-emerald-600 font-light">Pay at the front desk on or before your check-in date.</p></div>
                  </div>
                  <div class="flex items-center gap-2.5 bg-gray-50 border border-gray-100 rounded-lg px-3 py-2">
                    <span class="text-base leading-none">🏦</span>
                    <div><p class="text-xs font-semibold text-gray-800">Bank Transfer</p><p class="text-xs text-gray-500 font-light">Transfer to our account and send a receipt photo as confirmation.</p></div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- PHASE D: Checking Booking Status -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <div class="w-6 h-6 rounded-full bg-violet-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">D</div>
            <h3 class="text-xs font-bold text-violet-700 uppercase tracking-widest">Checking Your Booking Status</h3>
            <div class="flex-1 h-px bg-violet-100"></div>
          </div>

          <!-- Step 10 -->
          <div class="bg-white border border-gray-100 rounded-xl p-4 flex gap-3 shadow-sm mb-2">
            <div class="w-8 h-8 rounded-lg bg-violet-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">10</div>
            <div class="min-w-0">
              <p class="text-xs font-semibold text-gray-800 mb-1">Check Your Reservation Status</p>
              <p class="text-xs text-gray-500 font-light leading-relaxed">Contact us with your <strong class="text-violet-600">Reference Number</strong>. Our staff will tell you the current status of your booking.</p>
            </div>
          </div>

          <!-- Status reference -->
          <div class="space-y-1.5">
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
              <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div>
                <span class="inline-block bg-amber-400 text-white text-xs font-bold px-2 py-0.5 rounded-full mb-1">⏳ PENDING</span>
                <p class="text-xs text-amber-800 font-light leading-relaxed">Being reviewed. No action needed — wait for an update.</p>
              </div>
            </div>
            <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
              <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div>
                <span class="inline-block bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full mb-1">✅ APPROVED</span>
                <p class="text-xs text-green-800 font-light leading-relaxed">Booking confirmed! Proceed with your down payment to fully secure your stay.</p>
              </div>
            </div>
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
              <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div>
                <span class="inline-block bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full mb-1">❌ REJECTED</span>
                <p class="text-xs text-red-800 font-light leading-relaxed">Could not be accommodated. Contact us to try a different date or room.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Help banner -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-4">
          <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          </div>
          <div class="flex-1 text-center sm:text-left">
            <p class="text-white font-semibold text-sm mb-0.5">Need Help?</p>
            <p class="text-blue-100 text-xs font-light leading-relaxed">Can't find an answer? Reach out via the Contact Us section or visit us at the hotel.</p>
          </div>
          <a href="#contact" @click="showGuestManual = false" class="flex-shrink-0 bg-white text-green-800 font-semibold text-xs px-5 py-2 rounded-full hover:bg-green-50 transition shadow">Contact Us</a>
        </div>

      </div><!-- end scrollable content -->
      </div><!-- end modal box -->
    </div><!-- end modal overlay -->
  </Transition><!-- end gm-modal -->

</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import { useToastStore } from '../stores/toast'

const toast = useToastStore()

const router = useRouter()
const mobileNavOpen = ref(false)
const showGuestManual = ref(false)

const showBookingModal = ref(false)
const showEventModal = ref(false)
const rooms = ref([])
const eventPackages = ref([])
const selectedRoom = ref(null)

// Gallery
const galleryAlbums        = ref([])
const galleryOpenAlbum     = ref(null)
const galleryLightboxPhoto = ref(null)

const UPLOADS_BASE = (import.meta.env.VITE_UPLOADS_URL || 'http://localhost:8000/uploads').replace(/\/$/, '')

function publicPhotoUrl(filename) {
  return `${UPLOADS_BASE}/gallery/${filename}`
}

async function fetchPublicGallery() {
  try {
    const res = await api.get('/public/gallery')
    galleryAlbums.value = (res.data.data ?? res.data ?? []).filter(a => a.photos?.length)
  } catch {}
}

const contactInfo = ref({
  hotel_name:    "Joanna's Nook Bed & Breakfast",
  hotel_phone:   '',
  hotel_address: '',
  hotel_email:   '',
})
const selectedRoomGroup = ref(null)
const selectedEvent = ref(null)
const pkgCarouselIndex = ref(0)
const serviceRoomIndex = ref(0)
const serviceEventIndex = ref(0)

// About Us count-up stats
const statRooms = ref(0)
const statPackages = ref(0)
const statSatisfaction = ref(0)

const roomGroups = computed(() => {
  const map = {}
  rooms.value.forEach(r => {
    if (!map[r.name]) {
      map[r.name] = {
        ...r,
        roomList: [],
        availableCount: 0,
        uploadedImages: [],
      }
    }
    map[r.name].roomList.push(r)
    if (r.status === 'available') map[r.name].availableCount++
    if (r.uploadedImageUrl && !map[r.name].uploadedImages.includes(r.uploadedImageUrl)) {
      map[r.name].uploadedImages.push(r.uploadedImageUrl)
    }
  })
  return Object.values(map).map(g => ({
    ...g,
    images: g.uploadedImages.length > 0 ? g.uploadedImages : g.images,
    activeImage: 0,
  }))
})

// Room booking form
const selectedRoomId = ref(null)
const agreedPrivacy = ref(false)
const agreedTerms = ref(false)
const termsScrolled = ref(false)
const termsBoxRef = ref(null)

// Event agreement scroll tracking
const eventTermsScrolled = ref(false)
const eventTermsBoxRef = ref(null)

// Today's date string (YYYY-MM-DD) for min date on date inputs
const todayDate = computed(() => new Date().toISOString().split('T')[0])

// Scroll handlers – unlock checkboxes when user reaches the bottom
const handleTermsScroll = () => {
  if (termsScrolled.value) return
  const el = termsBoxRef.value
  if (!el) return
  if (el.scrollTop + el.clientHeight >= el.scrollHeight - 4) {
    termsScrolled.value = true
  }
}

const handleEventTermsScroll = () => {
  if (eventTermsScrolled.value) return
  const el = eventTermsBoxRef.value
  if (!el) return
  if (el.scrollTop + el.clientHeight >= el.scrollHeight - 4) {
    eventTermsScrolled.value = true
  }
}
const bookingSubmitting = ref(false)
const bookingSuccessRef = ref('')
const bookingForm = ref({
  name: '', email: '', contact: '', address: '',
  country: '', additionalGuests: 0,
  checkIn: '', checkOut: '', idFile: null, requests: ''
})

// Sync selectedRoom when dropdown changes
const onRoomSelect = () => {
  selectedRoom.value = rooms.value.find(r => r.id === Number(selectedRoomId.value)) || null
}

const computedNights = computed(() => {
  if (!bookingForm.value.checkIn || !bookingForm.value.checkOut) return 1
  const d = (new Date(bookingForm.value.checkOut) - new Date(bookingForm.value.checkIn)) / 86400000
  return d > 0 ? d : 1
})
const computedTotal = computed(() => {
  const price = selectedRoom.value?.priceNum || 0
  return price * computedNights.value
})

// Event reservation form
const eventAgreedPrivacy = ref(false)
const eventAgreedTerms = ref(false)
const eventSubmitting = ref(false)
const eventSuccessRef = ref('')
const eventForm = ref({
  eventName: '', eventType: '', name: '', email: '',
  contact: '', address: '', supervisor: '', eventDate: '',
  fromTime: '', guestCount: '', selectedPackageId: null, selectedPackage: null,
  selectedSetId: null, selectedSet: null, selectedFoods: [],
  requests: '', remarks: '', idFile: null
})

const buffetSets = [
  {
    id: 1, label: 'Set A', price: 500, desc: 'Up to 5 dishes',
    foods: [
      { category: 'Appetizer',  items: ['Lumpiang Shanghai', 'Spring Rolls'] },
      { category: 'Main (Meat)', items: ['Chicken Adobo', 'Pork BBQ', 'Lechon Kawali'] },
      { category: 'Vegetables', items: ['Pinakbet', 'Chopsuey'] },
      { category: 'Noodles',   items: ['Pansit Bihon'] },
      { category: 'Dessert',   items: ['Leche Flan', 'Fruit Salad'] },
    ]
  },
  {
    id: 2, label: 'Set B', price: 650, desc: 'Up to 6 dishes',
    foods: [
      { category: 'Appetizer',  items: ['Lumpiang Shanghai', 'Spring Rolls', 'Dynamite Lumpia'] },
      { category: 'Main (Meat)', items: ['Chicken Adobo', 'Pork BBQ', 'Lechon Kawali', 'Beef Caldereta'] },
      { category: 'Main (Seafood)', items: ['Grilled Bangus', 'Sweet & Sour Fish'] },
      { category: 'Vegetables', items: ['Pinakbet', 'Chopsuey', 'Laing'] },
      { category: 'Noodles',   items: ['Pansit Bihon', 'Pansit Canton'] },
      { category: 'Dessert',   items: ['Leche Flan', 'Buko Pandan', 'Fruit Salad'] },
    ]
  },
  {
    id: 3, label: 'Set C', price: 800, desc: 'Up to 7 dishes',
    foods: [
      { category: 'Appetizer',  items: ['Lumpiang Shanghai', 'Spring Rolls', 'Dynamite Lumpia', 'Kuchay Dumplings'] },
      { category: 'Main (Meat)', items: ['Chicken Adobo', 'Pork BBQ', 'Lechon Kawali', 'Beef Caldereta', 'Chicken Inasal', 'Kare-Kare'] },
      { category: 'Main (Seafood)', items: ['Grilled Bangus', 'Sweet & Sour Fish', 'Sinigang na Hipon'] },
      { category: 'Vegetables', items: ['Pinakbet', 'Chopsuey', 'Laing'] },
      { category: 'Pasta/Noodles', items: ['Pansit Bihon', 'Pansit Canton', 'Spaghetti'] },
      { category: 'Dessert',   items: ['Leche Flan', 'Buko Pandan', 'Halo-Halo', 'Fruit Salad'] },
    ]
  },
  {
    id: 4, label: 'Set D', price: 950, desc: 'Up to 8 dishes',
    foods: [
      { category: 'Appetizer',  items: ['Lumpiang Shanghai', 'Spring Rolls', 'Dynamite Lumpia', 'Kuchay Dumplings'] },
      { category: 'Main (Meat)', items: ['Chicken Adobo', 'Pork BBQ', 'Lechon Kawali', 'Beef Caldereta', 'Chicken Inasal', 'Kare-Kare', 'Crispy Pata'] },
      { category: 'Main (Seafood)', items: ['Grilled Bangus', 'Sweet & Sour Fish', 'Sinigang na Hipon', 'Steamed Fish w/ Tausi'] },
      { category: 'Vegetables', items: ['Pinakbet', 'Chopsuey', 'Laing', 'Ginisang Gulay'] },
      { category: 'Pasta/Noodles', items: ['Pansit Bihon', 'Pansit Canton', 'Spaghetti', 'Carbonara'] },
      { category: 'Dessert',   items: ['Leche Flan', 'Buko Pandan', 'Halo-Halo', 'Fruit Salad', 'Mango Float'] },
    ]
  },
]

const timeSlots = [
  '6:00 AM','7:00 AM','8:00 AM','9:00 AM','10:00 AM','11:00 AM',
  '12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM',
  '6:00 PM','7:00 PM','8:00 PM','9:00 PM','10:00 PM'
]

const eventComputedTotal = computed(() => {
  const pkgPrice  = eventForm.value.selectedPackage?.priceNum || 0
  const setPrice  = eventForm.value.selectedSet?.price || 0
  const guests    = parseInt(eventForm.value.guestCount) || 0
  return pkgPrice + (setPrice * guests)
})

// ── Image pools ──────────────────────────────────────────────────────────────
// Local fallback used for all room types when no image has been uploaded yet
const ROOM_PLACEHOLDER = '/Backimagehomepage.jpg'
const roomImagePools = {
  triple:   [ROOM_PLACEHOLDER],
  deluxe:   [ROOM_PLACEHOLDER],
  suite:    [ROOM_PLACEHOLDER],
  standard: [ROOM_PLACEHOLDER],
  default:  [ROOM_PLACEHOLDER],
}

// Local event package images (files in /public/Event Packages/)
const eventImageMap = {
  basic:    ['/Event Packages/Basic Package.jpg',    '/Event Packages/Calamares.webp'],
  standard: ['/Event Packages/Standard Package.jpg', '/Event Packages/Calamares.webp'],
  premium:  ['/Event Packages/Premium package.jpg',  '/Event Packages/Calamares.webp'],
  deluxe:   ['/Event Packages/Deluxe Package.jpg',   '/Event Packages/Calamares.webp'],
  super:    ['/Event Packages/Super Package.jpg',     '/Event Packages/Calamares.webp'],
}
const eventImageFallback = [
  '/Event Packages/Basic Package.jpg',
  '/Event Packages/Standard Package.jpg',
  '/Event Packages/Premium package.jpg',
  '/Event Packages/Deluxe Package.jpg',
  '/Event Packages/Super Package.jpg',
]

function getEventImages(packageName) {
  const n = (packageName || '').toLowerCase()
  for (const key of Object.keys(eventImageMap)) {
    if (n.includes(key)) return eventImageMap[key]
  }
  return eventImageFallback
}

function getRoomImages(type) {
  const t = (type || '').toLowerCase()
  if (t.includes('triple'))  return roomImagePools.triple
  if (t.includes('deluxe'))  return roomImagePools.deluxe
  if (t.includes('suite'))   return roomImagePools.suite
  return roomImagePools.standard
}

function getRoomFeatures(type, description) {
  const t = (type || '').toLowerCase()
  const d = (description || '').toLowerCase()
  const features = []
  if (d.includes('breakfast') || t.includes('deluxe') || t.includes('suite')) features.push('Free Breakfast')
  features.push('Private Toilet & Bath')
  features.push('Air Conditioning')
  features.push('WiFi')
  if (t.includes('suite') || t.includes('deluxe')) features.push('Cable TV')
  return features.slice(0, 4)
}

function getPackageInclusions(name, description) {
  const n = (name || '').toLowerCase()
  const d = (description || '')
  // Parse newline/bullet separated inclusions from description if present
  const lines = d.split(/\n|,|;/).map(s => s.replace(/^[-•*✓]+\s*/, '').trim()).filter(s => s.length > 3 && s.length < 60)
  if (lines.length >= 2) return lines.slice(0, 6)
  // Tier-based defaults
  const base = ['Full Venue Access', 'Catering Services', 'Tables & Chairs Setup', 'Professional Staff']
  if (n.includes('standard')) return [...base, 'Lights & Sound System']
  if (n.includes('premium'))  return [...base, 'Lights & Sound System', 'Photo Booth', 'Event Coordinator']
  if (n.includes('deluxe'))   return [...base, 'Lights & Sound System', 'Photo Booth', 'Event Coordinator', 'Overnight Accommodation']
  return base
}

// Derive the backend base URL from the same env var used by the API service.
// When VITE_API_URL is a relative path (e.g. /api on production), keep it as-is
// so that image URLs resolve to /api/uploads/... correctly.
// When it is an absolute http URL (local dev), strip the trailing /api segment.
const _apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
const BACKEND_URL = _apiUrl.startsWith('http') ? _apiUrl.replace(/\/api$/, '') : _apiUrl

function mapRoom(r) {
  const fallbackImages = getRoomImages(r.type)
  const uploadedImageUrl = r.image_url ? `${BACKEND_URL}/${r.image_url}` : null
  const images = uploadedImageUrl ? [uploadedImageUrl] : [...fallbackImages]
  return {
    id: r.id,
    roomNumber: r.room_number,
    status: r.status || 'available',
    name: r.type,
    label: (r.type || '').toUpperCase(),
    priceNum: parseFloat(r.price) || 0,
    guests: r.capacity || 2,
    description: r.description || `Comfortable ${r.type} room with modern amenities.`,
    features: getRoomFeatures(r.type, r.description),
    images,
    uploadedImageUrl,
    activeImage: 0,
  }
}

function mapPackage(p, idx) {
  const fallbackImages = getEventImages(p.package_name)
  const images = p.image_url
    ? [`${BACKEND_URL}/${p.image_url}`]
    : [...fallbackImages]
  return {
    id: p.id,
    name: p.package_name,
    priceNum: parseFloat(p.price) || 0,
    guests: p.max_guests || 0,
    description: p.description || `An exclusive event package for up to ${p.max_guests} guests.`,
    inclusions: getPackageInclusions(p.package_name, p.description),
    images,
    activeImage: 0,
  }
}

// ── Map ───────────────────────────────────────────────────────────────────────
const mapRef = ref(null)

const initContactMap = () => {
  const L = window.L
  if (!L || !mapRef.value) return
  // Prevent double-init
  if (mapRef.value._leaflet_id) return
  const map = L.map(mapRef.value, { zoomControl: true, scrollWheelZoom: false }).setView([8.74436558174729, 124.781658936795], 17)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map)
  const icon = L.divIcon({
    className: '',
    html: `<div style="width:28px;height:44px"><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 36'><path d='M12 0C7.589 0 4 3.589 4 8c0 5.5 8 20 8 20s8-14.5 8-20c0-4.411-3.589-8-8-8z' fill='#2563eb'/><circle cx='12' cy='8' r='3.5' fill='#fff'/></svg></div>`,
    iconSize: [28, 44],
    iconAnchor: [14, 44],
    popupAnchor: [0, -44]
  })
  L.marker([8.74436558174729, 124.781658936795], { icon }).addTo(map).bindPopup('<b>Joanna\'s Nook Bed & Breakfast</b><br>Balingasag, Misamis Oriental').openPopup()
}

// ── Fetch ─────────────────────────────────────────────────────────────────────
onMounted(async () => {
  try {
    const [roomsRes, packagesRes, settingsRes] = await Promise.all([
      api.get('/public/rooms'),
      api.get('/public/event-packages'),
      api.get('/public/settings'),
    ])
    rooms.value = (roomsRes.data.data || []).map(mapRoom)
    eventPackages.value = (packagesRes.data.data || []).map(mapPackage)
    if (settingsRes.data?.data) {
      contactInfo.value = { ...contactInfo.value, ...settingsRes.data.data }
    }
  } catch (e) {
    console.error('Failed to load homepage data:', e)
  }
  // Gallery fetch is non-critical — don't block page load
  fetchPublicGallery()
  await nextTick()
  initContactMap()

  // About Us scroll-reveal
  const revealEls = document.querySelectorAll('.about-reveal')
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('about-reveal--visible')
        revealObserver.unobserve(entry.target)
      }
    })
  }, { threshold: 0.15 })
  revealEls.forEach(el => revealObserver.observe(el))

  // About Us timeline steps staggered reveal
  const timelineSteps = document.querySelectorAll('.about-timeline-step')
  const timelineObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = entry.target.style.getPropertyValue('--step-delay') || '0ms'
        setTimeout(() => {
          entry.target.classList.add('about-timeline-step--visible')
        }, parseInt(delay))
        timelineObserver.unobserve(entry.target)
      }
    })
  }, { threshold: 0.2 })
  timelineSteps.forEach(el => timelineObserver.observe(el))

  // About Us stats count-up
  function animateCount(refVal, target, duration) {
    const steps = 50
    const stepTime = duration / steps
    const increment = target / steps
    let current = 0
    const timer = setInterval(() => {
      current += increment
      if (current >= target) {
        refVal.value = target
        clearInterval(timer)
      } else {
        refVal.value = Math.floor(current)
      }
    }, stepTime)
  }
  const statsGrid = document.querySelector('.about-stats-grid')
  let statsTriggered = false
  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !statsTriggered) {
        statsTriggered = true
        animateCount(statRooms, 10, 1200)
        animateCount(statPackages, 5, 1000)
        animateCount(statSatisfaction, 100, 1400)
        statsObserver.unobserve(entry.target)
      }
    })
  }, { threshold: 0.3 })
  if (statsGrid) statsObserver.observe(statsGrid)

  // Services scroll-reveal
  const svcRevealEls = document.querySelectorAll('.svc-reveal')
  const svcRevealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('svc-reveal--visible')
        svcRevealObserver.unobserve(entry.target)
      }
    })
  }, { threshold: 0.12 })
  svcRevealEls.forEach(el => svcRevealObserver.observe(el))

  // Services feature cards staggered reveal
  const svcFeatureCards = document.querySelectorAll('.svc-feature-card')
  const svcFeatureObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const delay = parseInt(entry.target.style.getPropertyValue('--svc-delay') || '0')
        setTimeout(() => {
          entry.target.classList.add('svc-feature-card--visible')
        }, delay)
        svcFeatureObserver.unobserve(entry.target)
      }
    })
  }, { threshold: 0.2 })
  svcFeatureCards.forEach(el => svcFeatureObserver.observe(el))
})

// ── Modals ────────────────────────────────────────────────────────────────────
const openBookingForm = (group) => {
  selectedRoomGroup.value = group
  selectedRoomId.value = ''
  selectedRoom.value = null
  agreedPrivacy.value = false
  agreedTerms.value = false
  termsScrolled.value = false
  bookingForm.value = { name: '', email: '', contact: '', address: '', country: '', additionalGuests: 0, checkIn: '', checkOut: '', idFile: null, requests: '' }
  showBookingModal.value = true
}

const openEventForm = (eventId) => {
  selectedEvent.value = eventPackages.value.find(e => e.id === eventId)
  eventAgreedPrivacy.value = false
  eventAgreedTerms.value = false
  eventTermsScrolled.value = false
  const idx = eventPackages.value.findIndex(e => e.id === eventId)
  pkgCarouselIndex.value = idx >= 0 ? idx : 0
  eventForm.value = {
    eventName: '', eventType: '', name: '', email: '',
    contact: '', address: '', supervisor: '', eventDate: '',
    fromTime: '', guestCount: '', selectedPackageId: eventId, selectedPackage: selectedEvent.value,
    selectedSetId: null, selectedSet: null, selectedFoods: [],
    requests: '', remarks: '', idFile: null
  }
  showEventModal.value = true
}

const submitBooking = async () => {
  if (!agreedPrivacy.value || !agreedTerms.value) return
  bookingSubmitting.value = true
  try {
    const res = await api.post('/pending-reservations', {
      reservation_type:  'room',
      guest_name:        bookingForm.value.name,
      guest_email:       bookingForm.value.email,
      guest_contact:     bookingForm.value.contact,
      guest_address:     bookingForm.value.address,
      guest_country:     bookingForm.value.country,
      room_id:           selectedRoomId.value,
      check_in_date:     bookingForm.value.checkIn,
      check_out_date:    bookingForm.value.checkOut,
      number_of_guests:  bookingForm.value.additionalGuests || 1,
      special_requests:  bookingForm.value.requests,
      estimated_total:   computedTotal.value,
    })
    bookingSuccessRef.value = res.data?.data?.reference_number || 'Submitted'
  } catch (e) {
    toast.error(e.response?.data?.message || 'Failed to submit reservation. Please try again.')
  } finally {
    bookingSubmitting.value = false
  }
}

const submitEventInquiry = async () => {
  if (!eventAgreedPrivacy.value || !eventAgreedTerms.value) return
  eventSubmitting.value = true
  try {
    const res = await api.post('/pending-reservations', {
      reservation_type:  'event',
      guest_name:        eventForm.value.name,
      guest_email:       eventForm.value.email,
      guest_contact:     eventForm.value.contact,
      guest_address:     eventForm.value.address,
      event_name:        eventForm.value.eventName,
      event_type:        eventForm.value.eventType,
      event_date:        eventForm.value.eventDate,
      event_time:        eventForm.value.fromTime,
      event_package_id:  eventForm.value.selectedPackageId,
      buffet_set:        eventForm.value.selectedSet?.label || null,
      selected_foods:    eventForm.value.selectedFoods,
      supervisor:        eventForm.value.supervisor,
      number_of_guests:  eventForm.value.guestCount || 1,
      special_requests:  eventForm.value.requests,
      remarks:           eventForm.value.remarks,
      estimated_total:   eventComputedTotal.value,
    })
    eventSuccessRef.value = res.data?.data?.reference_number || 'Submitted'
  } catch (e) {
    toast.error(e.response?.data?.message || 'Failed to submit reservation. Please try again.')
  } finally {
    eventSubmitting.value = false
  }
}

const navigateToLogin = () => { router.push('/login') }


const scrollToSection = (sectionId) => {
  const el = document.getElementById(sectionId)
  if (el) el.scrollIntoView({ behavior: 'smooth' })
}

const nextImage = (item) => {
  item.activeImage = (item.activeImage + 1) % item.images.length
}

const prevImage = (item) => {
  item.activeImage = (item.activeImage - 1 + item.images.length) % item.images.length
}
</script>

<style scoped>
* {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
/* Guest Manual modal transitions */
.gm-modal-enter-active, .gm-modal-leave-active { transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.4,0,0.2,1); }
.gm-modal-enter-from,  .gm-modal-leave-to      { opacity: 0; transform: scale(0.95); }

/* ── Hero Section animations ─────────────────────────────────────────────── */

/* Ken Burns slow zoom */
.hero-bg {
  animation: heroBgZoom 18s ease-in-out infinite alternate;
  transform-origin: center center;
}
@keyframes heroBgZoom {
  0%   { transform: scale(1);    }
  100% { transform: scale(1.08); }
}

/* Top shimmer line */
.hero-shimmer-line {
  position: absolute;
  top: 0;
  left: -100%;
  width: 60%;
  height: 2px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent);
  z-index: 10;
  animation: heroShimmer 4s ease-in-out 0.5s forwards;
}
@keyframes heroShimmer {
  0%   { left: -60%; opacity: 0; }
  20%  { opacity: 1; }
  100% { left: 140%; opacity: 0; }
}

/* Generic entrance animations */
.hero-anim {
  opacity: 0;
  animation-fill-mode: forwards;
  animation-duration: 0.85s;
  animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  animation-delay: var(--h-delay, 0ms);
}
.hero-anim--fade {
  animation-name: heroFadeIn;
}
.hero-anim--up {
  transform: translateY(28px);
  animation-name: heroSlideUp;
}
@keyframes heroFadeIn {
  to { opacity: 1; }
}
@keyframes heroSlideUp {
  to { opacity: 1; transform: translateY(0); }
}

/* Animated accent line under heading */
.hero-accent-line {
  height: 1px;
  width: 0;
  background: rgba(255,255,255,0.5);
  animation: heroAccentExpand 0.9s cubic-bezier(0.4, 0, 0.2, 1) var(--h-delay, 750ms) forwards;
}
@keyframes heroAccentExpand {
  to { width: 80px; }
}

/* Scroll indicator */
.hero-scroll-indicator {
  animation: heroScrollFadeIn 1s ease 1.6s forwards;
  opacity: 0;
}
@keyframes heroScrollFadeIn {
  to { opacity: 1; }
}
.hero-scroll-line {
  width: 1px;
  height: 0;
  background: white;
  animation: heroScrollGrow 1s ease 1.8s forwards;
}
@keyframes heroScrollGrow {
  to { height: 32px; }
}

/* ── About Us scroll-reveal animations ────────────────────────────────────── */
.about-reveal {
  opacity: 0;
  transition: opacity 0.75s ease, transform 0.75s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: var(--delay, 0ms);
}
.about-reveal--up    { transform: translateY(40px); }
.about-reveal--left  { transform: translateX(-50px); }
.about-reveal--right { transform: translateX(50px); }

.about-reveal--visible {
  opacity: 1;
  transform: translate(0, 0);
}

/* ── About Us image hover parallax ── */
.about-img-wrapper:hover .about-img {
  transform: scale(1.03) translateY(-4px);
}

/* ── Floating badge animation ── */
.about-float-badge {
  animation: aboutFloatBadge 3.5s ease-in-out infinite;
}
@keyframes aboutFloatBadge {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(-6px); }
}

/* ── Pulsing decorative ring behind image ── */
.about-pulse-ring {
  position: absolute;
  inset: -12px;
  border: 1.5px solid rgba(21, 128, 61, 0.25);
  z-index: 0;
  animation: aboutPulseRing 3s ease-in-out infinite;
}
@keyframes aboutPulseRing {
  0%, 100% { opacity: 0.4; transform: scale(1); }
  50%       { opacity: 1;   transform: scale(1.015); }
}

/* ── Timeline step reveal ── */
.about-timeline-step {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.65s ease, transform 0.65s cubic-bezier(0.4, 0, 0.2, 1);
}
.about-timeline-step--visible {
  opacity: 1;
  transform: translateY(0);
}

/* ── Timeline icon bounce-in ── */
.about-timeline-step--visible .about-timeline-icon {
  animation: aboutIconPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes aboutIconPop {
  0%   { transform: scale(0.5); opacity: 0; }
  100% { transform: scale(1);   opacity: 1; }
}

/* ── Services Section animations ─────────────────────────────────────────── */
.svc-reveal {
  opacity: 0;
  transition: opacity 0.7s ease, transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: var(--svc-delay, 0ms);
}
.svc-reveal--up    { transform: translateY(36px); }
.svc-reveal--left  { transform: translateX(-44px); }
.svc-reveal--right { transform: translateX(44px); }
.svc-reveal--visible {
  opacity: 1;
  transform: translate(0, 0);
}

/* Card image hover zoom */
.svc-card-img-wrap img {
  transition: transform 0.65s cubic-bezier(0.4, 0, 0.2, 1);
}
.svc-card-img-wrap:hover img {
  transform: scale(1.07);
}

/* Feature card staggered reveal */
.svc-feature-card {
  opacity: 0;
  transform: translateY(32px);
  transition: opacity 0.65s ease, transform 0.65s cubic-bezier(0.4, 0, 0.2, 1);
}
.svc-feature-card--visible {
  opacity: 1;
  transform: translateY(0);
}

/* Feature icon pop when card becomes visible */
.svc-feature-card--visible .svc-feature-icon {
  animation: svcIconPop 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes svcIconPop {
  0%   { transform: scale(0.55) rotate(-12deg); opacity: 0; }
  100% { transform: scale(1)    rotate(0deg);   opacity: 1; }
}

/* Gallery album card hover elevation */
.gallery-album-card {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
}
.gallery-album-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px -8px rgba(0, 0, 0, 0.15), 0 8px 16px -4px rgba(0, 0, 0, 0.08);
}

/* Gallery album modal slide-up transition */
.gallery-modal-enter-active {
  transition: opacity 0.3s ease, transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.gallery-modal-leave-active {
  transition: opacity 0.25s ease, transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.gallery-modal-enter-from,
.gallery-modal-leave-to {
  opacity: 0;
  transform: translateY(40px);
}
</style>
