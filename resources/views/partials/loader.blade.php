<!-- Page Loader Overlay -->
<style>
    #page-loader {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: #111827;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 16px;
        transition: opacity 0.3s;
        opacity: 0;
        pointer-events: none;
    }
    #page-loader.show { opacity: 1; pointer-events: all; }
    .loader-logo { font-size: 26px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
    .loader-logo span { color: var(--accent); }
    .loader-bar { width: 120px; height: 3px; background: rgba(255, 255, 255, 0.1); border-radius: 2px; overflow: hidden; }
    .loader-bar-fill { height: 100%; width: 0%; background: linear-gradient(90deg, var(--accent), var(--accent-hover)); border-radius: 2px; animation: ldrSlide 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; }

    @keyframes ldrSlide { 0% { width: 0% } 60% { width: 80% } 100% { width: 100% } }
</style>
<div id="page-loader">
    <div class="loader-logo"><span>HUGO</span> - Assistant</div>


    <div class="loader-bar">
        <div class="loader-bar-fill"></div>
    </div>
</div>
