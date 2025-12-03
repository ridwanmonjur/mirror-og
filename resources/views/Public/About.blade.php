@extends('layout.app')

@section('title', 'OW Gaming - About Us')
@section('body-class', 'about')
@push('styles')
    <meta name="description" content="Learn about OW Gaming - the esports community platform connecting competitive gamers, organizing tournaments, and building a lasting ecosystem for gaming enthusiasts worldwide.">
    <meta name="keywords" content="esports community, gaming platform, competitive gaming, esports tournaments, gaming community, LAN cafes, esports ecosystem">

    <!-- Open Graph tags -->
    <meta property="og:title" content="About OW Gaming | Esports Community Platform - Bringing Gamers Together">
    <meta property="og:description" content="Learn about OW Gaming - the esports community platform connecting competitive gamers, organizing tournaments, and building a lasting ecosystem for gaming enthusiasts worldwide.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://driftwood.gg/about">
    <meta property="og:image" content="https://driftwood.gg/images/assets/images/DW_LOGO.png">

    <!-- Twitter Card tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="About OW Gaming | Esports Community Platform">
    <meta name="twitter:description" content="Learn about OW Gaming - the esports community platform connecting competitive gamers">
    <meta name="twitter:image" content="https://driftwood.gg/images/assets/images/DW_LOGO.png"> 
    
    <style>
        .policy-row {
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }
        
        .policy-row:last-child {
            border-bottom: none;
        }
        
        .policy-svg {
            width: 70px;
            height: 70px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .policy-svg:hover {
            transform: scale(1.1);
        }

        .grid-template-columns {
            display: grid;
            grid-auto-rows: 1fr;
            grid-template-columns: 1fr;
        }

        @media screen and (min-width: 992px) { 
            .grid-template-columns {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        .button-down {
            position: absolute;
            top: 90%;
            left: 0%;
            margin-bottom: 20px;
        }
        
    </style>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "OW Gaming",
            "url": "https://driftwood.gg",
            "logo": "https://driftwood.gg/assets/images/DW_LOGO.png",
            "description": "OW Gaming is an esports community platform bringing competitive gamers together through tournaments, events, and community building.",
            "sameAs": [
                "https://twitter.com/OW GamingEsports",
                "https://facebook.com/OW GamingEsports",
                "https://linkedin.com/company/driftwood-esports"
            ]
        }
    </script>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "AboutPage",
            "mainEntity": {
                "@type": "Organization",
                "name": "OW Gaming",
                "description": "Esports community platform connecting competitive gamers"
            }
        }
    </script>

@endpush
@section('content')
    <header>
        @include('includes.Navbar')
       
    </header>
    <main style="padding: 5vh 10vw ;">
        {{-- Para 1 --}}
        <section class="mb-5">
            <h1 class="display-4 fw-light text-white mb-4">What is OW Gaming about?</h1>
            <p class="text-white mb-3">OW Gaming is about bringing people who love esports closer together.</p>
            <p class="text-white mb-3 text-justify">To us, esports is something that can form bonds between friends, family, and
                community. We want to share our passion for esports with those around us, and we want to enable you to share
                your passion with those around you.</p>
        </section>
        {{-- Para 2 --}}
        <section>
            <h2 class="display-4 px-0 fw-light text-white mb-4">How did OW Gaming begin?</h2>
            <p class="text-white px-0 mb-3 text-justify">Our team grew up playing and loving competitive games. Late nights at LAN cafes after
                school, laughing and shouting with friends, getting excited at small victories, and feeling defeated after
                clean sweeps.</p>
            <p class="text-white px-0 mb-3 text-justify">It's been years since we've been in the competitive sphere. Yet, the struggles faced
                by new and amateur players today are the same as what we faced over a decade ago. Passionate players start
                clubs and run tournaments, creating vibrant spaces and communities, but many of these grassroots efforts
                aren't able to connect with the people and businesses outside of their spaces. One by one as founders grow
                older, people move on, and efforts borne from passion are left to fall into disrepair.</p>
            <p class="text-white px-0 mb-3">So we thought, if there hasn't been change, then let's create change.</p>
            <p class="text-white px-0 mb-3 text-justify">That is how OW Gaming came to be. We want a place for esports fans to come together
                and connect with people both inside and outside of the gaming space, and to create a lasting ecosystem
                around the games we love.</p>
        </section>
        <section class=" mt-5">
            <h2 class="display-4 px-0 fw-light text-white mb-4">Legal Guidelines</h2>
            <!-- Privacy Policy Row -->
            <div class="grid-template-columns">
                <article class="policy-row position-relative text-white">
                    <div class="px-2">
                        <svg class="policy-svg"  data-pdf="/assets/pdf/OW Gaming Privacy Policy (Apr 2025).pdf" data-title="Privacy Policy" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="#ffffff" stroke="#5fb7e6" stroke-width="2"/>
                            <circle cx="50" cy="35" r="15" fill="#5fb7e6"/>
                            <path d="M50,55 C35,55 25,65 25,80 L75,80 C75,65 65,55 50,55 Z" fill="#5fb7e6"/>
                        </svg>
                    </div>
                    <div class="px-2">
                        <h3 class="mt-2">Privacy Policy</h3>
                        <p>Learn how we collect, use, and protect your personal information, safeguarding mutual interest.</p>
                    </div>
                    <div class="px-2 ">
                        <button 
                            onclick="openNewTab(event);"
                            class="btn button-down mt-2 mb-4 bg-support-btn text-white rounded-pill px-4 py-2"  data-pdf="/assets/pdf/OW Gaming Privacy Policy (Apr 2025).pdf" data-title="Privacy Policy">
                            View Policy
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                            </svg>
                        </button>
                    </div>
                </article>
                
                <!-- Return Policy Row -->
                <article class="policy-row position-relative text-white">
                    <div class=" px-2">
                        <svg class="policy-svg"  data-pdf="/assets/pdf/OW Gaming Return Policy (Apr 2025).pdf" data-title="Return Policy" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                            <!-- Simple circular background -->
                            <circle cx="50" cy="50" r="45" fill="#ffffff" stroke="#5ebb7d" stroke-width="2"/>
                            
                            <!-- Simple return arrow -->
                            <path d="M30,50 L50,30 L50,40 C65,40 70,50 70,65 C60,50 50,50 50,50 L50,60 L30,50 Z" fill="#5ebb7d"/>
                            </svg>
                    </div>
                    <div class="px-2">
                        <h3 class="mt-2">Return Policy</h3>
                        <p>Information about our return procedures, refunds, and exchanges.</p>
                    </div>
                    <div class="px-2 ">
                        <button 
                            onclick="openNewTab(event);"
                            class="button-down btn mt-2 mb-4 bg-general-btn text-white rounded-pill px-4 py-2"  data-pdf="/assets/pdf/OW Gaming Return Policy (Apr 2025).pdf" data-title="Return Policy">
                            View Policy
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                            </svg>
                        </button>
                    </div>
                </article>
                
                <!-- Terms and Conditions Row -->
                <article class="policy-row position-relative text-white">
                    <div class="px-2">
                        <svg class="policy-svg"  data-pdf="/assets/pdf/OW Gaming Terms and Conditions (Apr 2025).pdf" data-title="Terms and Conditions" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="#ffffff" stroke="#64DBAF" stroke-width="2"/>
                            <rect x="30" y="25" width="40" height="50" fill="#64DBAF" rx="3"/>
                            <line x1="37" y1="35" x2="63" y2="35" stroke="white" stroke-width="2"/>
                            <line x1="37" y1="45" x2="63" y2="45" stroke="white" stroke-width="2"/>
                            <line x1="37" y1="55" x2="63" y2="55" stroke="white" stroke-width="2"/>
                            <line x1="37" y1="65" x2="55" y2="65" stroke="white" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="px-2">
                        <h3 class="mt-2">Terms and Conditions</h3>
                        <p>The legal agreement between you and OW Gaming when using our services.</p>
                    </div>
                    <div class="px-2">
                        <button 
                            onclick="openNewTab(event);"
                            class="button-down btn mt-2 mb-4 bg-purple-btn text-dark rounded-pill px-4 py-2"  data-pdf="/assets/pdf/OW Gaming Terms and Conditions (Apr 2025).pdf" data-title="Terms and Conditions">
                            View T&C 
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                            </svg>
                        </button>
                    </div>
                </article>
            </div>
        </section>
        
    </main>
   <script>
        function openNewTab(event) {
             const pdfPath = event.target.getAttribute('data-pdf');
        
            const isMobileView = window.matchMedia('(max-width: 768px)').matches;
            
            if (isMobileView) {
                const link = document.createElement('a');
                link.href = pdfPath;
                link.download = pdfPath.split('/').pop(); 
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                window.open(pdfPath, '_blank', 'noopener,noreferrer');
            }
        }
    </script>
@endsection

