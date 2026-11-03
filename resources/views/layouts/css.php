<style>
    /* Overlay: fondo oscuro y centra el loader */
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.95);
        /* <-- más oscuro */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s, visibility 0.3s;
    }

    .loader-overlay.active {
        visibility: visible;
        opacity: 1;
    }

    /* Tu loader animado, igual que antes */
    .loader {
        position: relative;
        width: 120px;
        height: 90px;
        margin: 0 auto;
    }

    .loader:before {
        content: "";
        position: absolute;
        bottom: 30px;
        left: 50px;
        height: 30px;
        width: 30px;
        border-radius: 50%;
        background: #2a9d8f;
    }

    .loader:after {
        content: "";
        position: absolute;
        right: 0;
        top: 0;
        height: 7px;
        width: 45px;
        border-radius: 4px;
        box-shadow: 0 5px 0 #f2f2f2, -35px 50px 0 #f2f2f2, -70px 95px 0 #f2f2f2;
    }

    

    

    /* Notificaciones */

    /* Bell badge */
    .notification-badge {
        position: absolute;
        top: 3px;
        right: 1px;
        background: #d01919;
        color: #fff;
        border-radius: 50%;
        font-size: 0.75em;
        padding: 2px 6px;
        min-width: 18px;
        text-align: center;
        font-weight: bold;
        line-height: 1.2;
    }

    .notification-link {
        color: inherit !important;
        text-decoration: none !important;
        display: block;
        padding: 0.8rem 0;
    }

    .notification-link:hover {
        background-color: #f9f9f9;
    }

    .notification-link .header {
        font-weight: bold;
        color: #000 !important;
    }

    .notification-link .description {
        color: #444;
        font-size: 0.9em;
        margin-top: 4px;
    }

    .notification-link small {
        color: #777 !important;
        font-size: 0.8em;
    }
</style>