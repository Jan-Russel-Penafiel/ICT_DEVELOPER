<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'SKSU-IOMS Service Portal') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root{--ink:#15231e;--muted:#607069;--line:#dfe7e2;--surface:#fff;--canvas:#f5f7f5;--forest:#087b43;--forest-dark:#056237;--mint:#eaf5ef;--danger:#b42318;--page-container-width:min(1320px,calc(100% - 32px))}*{box-sizing:border-box}body{margin:0;background:var(--canvas);color:var(--ink);font:14px/1.5 Inter,"Segoe UI",Arial,sans-serif}.site-header{background:transparent;border-bottom:1px solid transparent;color:var(--forest-dark);box-shadow:none}.nav-shell{width:var(--page-container-width);min-height:49px;margin:auto;display:flex;align-items:center;gap:14px}.brand{display:inline-flex;align-items:center;gap:8px;color:var(--forest-dark);text-decoration:none;white-space:nowrap}.brand img{width:32px;height:32px;object-fit:contain}.brand-copy{display:grid;line-height:1.05}.brand-copy strong{font-size:12px;letter-spacing:.04em}.brand-copy small{margin-top:3px;color:#458467;font-size:8px;letter-spacing:.08em}.site-nav{display:flex;align-items:center;gap:2px;min-width:0}.site-nav a{color:var(--forest-dark);text-decoration:none;padding:7px 9px;border-radius:5px;font-size:12px;font-weight:700;white-space:nowrap}.site-nav a:hover,.site-nav a:focus-visible{background:rgba(8,123,67,.1);outline:0}.site-nav .nav-action{background:var(--forest);border:1px solid var(--forest);color:#fff}.site-nav .nav-action:hover{background:var(--forest-dark);border-color:var(--forest-dark)}.account-menu{position:relative;margin-left:auto}.account-menu summary{display:flex;align-items:center;gap:7px;min-height:34px;padding:4px 9px 4px 5px;border:1px solid rgba(8,123,67,.35);border-radius:6px;color:var(--forest-dark);cursor:pointer;list-style:none}.account-menu summary::-webkit-details-marker{display:none}.profile-avatar{display:grid;place-items:center;width:24px;height:24px;border-radius:50%;background:var(--forest);color:#fff;font-size:11px;font-weight:850}.profile-name{max-width:118px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;font-weight:750}.profile-caret{font-size:11px}.account-menu-panel{position:absolute;z-index:20;right:0;top:calc(100% + 7px);width:205px;padding:10px;border:1px solid #dbe8e0;border-radius:7px;background:#fff;color:var(--ink);box-shadow:0 12px 28px rgba(14,40,26,.18)}.account-menu-panel p{margin:0 0 8px;padding:0 4px;font-size:12px}.account-menu-panel .account-caption{display:block;color:var(--muted);font-size:10px;font-weight:750;text-transform:uppercase;letter-spacing:.06em}.account-menu-panel a{display:block;padding:8px;border-radius:5px;color:var(--ink);font-size:12px;text-decoration:none}.account-menu-panel a:hover{background:var(--mint);color:var(--forest-dark)}.wrap{width:var(--page-container-width);margin:26px auto 48px}.panel{margin-bottom:20px;padding:24px;background:var(--surface);border:1px solid var(--line);border-radius:8px;box-shadow:0 1px 2px rgba(15,35,26,.04)}h1,h2{margin:0 0 9px;line-height:1.2;letter-spacing:-.025em}h1{font-size:27px}h2{font-size:18px}p{margin:0 0 14px}.muted{color:var(--muted)}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.full{grid-column:1/-1}label{display:grid;gap:6px;font-size:13px;font-weight:700}input,select,textarea{width:100%;min-height:40px;padding:9px 10px;border:1px solid #b9c7bf;border-radius:5px;background:#fff;color:var(--ink);font:inherit}textarea{min-height:auto;resize:vertical}input:focus,select:focus,textarea:focus{outline:3px solid #b9e4d1;border-color:var(--forest)}.button{display:inline-flex;justify-content:center;align-items:center;min-height:36px;padding:8px 13px;border:1px solid var(--forest);border-radius:6px;background:var(--forest);color:#fff;font:700 12px/1 inherit;text-decoration:none;cursor:pointer}.button:hover{background:var(--forest-dark);border-color:var(--forest-dark)}.button.secondary{background:#fff;color:var(--forest)}a{color:var(--forest);font-weight:650}.table-wrap{overflow-x:auto;padding:0}table{width:100%;border-collapse:collapse;font-size:13px}th,td{padding:12px 14px;border-bottom:1px solid var(--line);vertical-align:top;text-align:left}th{background:#f1f6f3;color:#466055;font-size:11px;letter-spacing:.06em;text-transform:uppercase}tr:last-child td{border-bottom:0}.chip{display:inline-flex;padding:3px 8px;border-radius:999px;background:var(--mint);color:var(--forest-dark);font-size:11px;font-weight:800;text-transform:capitalize}.notice{margin-bottom:16px;padding:12px 14px;border:1px solid #acd7bf;border-left:4px solid var(--forest);border-radius:5px;background:var(--mint)}.notice.error{border-color:#f3c2be;border-left-color:var(--danger);background:#fff2f1;color:#7a271a}.modal{width:min(620px,calc(100% - 28px));max-height:calc(100dvh - 40px);padding:0;border:0;border-radius:10px;color:var(--ink);box-shadow:0 24px 70px rgba(5,32,19,.32)}.modal::backdrop{background:rgba(8,27,17,.52);backdrop-filter:blur(2px)}.modal-header{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:20px 22px 0}.modal-header h2{font-size:18px;margin:0}.modal-close{width:30px;min-height:30px;padding:0;border:0;border-radius:50%;background:#eff5f1;color:#355145;font-size:20px;line-height:1;cursor:pointer}.modal-body{padding:18px 22px 22px}.modal-footer{display:flex;justify-content:flex-end;gap:8px;margin:20px 0 0}.homepage-track{display:flex;align-items:end;gap:10px;max-width:580px;margin-top:18px}.homepage-track label{flex:1}@media(max-width:900px){.nav-shell{min-height:50px;padding:8px 0;flex-wrap:wrap}.site-nav{overflow-x:auto;order:2;flex:1}.account-menu{order:1}.wrap{margin-top:20px}}@media(max-width:520px){.grid{grid-template-columns:1fr}.panel{padding:18px}.brand-copy small{display:none}.site-nav a{padding:6px 7px;font-size:11px}.modal-header,.modal-body{padding-left:18px;padding-right:18px}.profile-name{display:none}.homepage-track{align-items:stretch;flex-direction:column}}

        /* Thin scrollbars - Emerald Green from palette */
        *{scrollbar-width:thin;scrollbar-color:var(--forest) transparent}*::-webkit-scrollbar{width:6px;height:6px}*::-webkit-scrollbar-track{background:transparent}*::-webkit-scrollbar-thumb{background:var(--forest);border-radius:3px}*::-webkit-scrollbar-thumb:hover{background:var(--forest-dark)}*::-webkit-scrollbar-corner{background:transparent}
    </style>
    <style>
        .page-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin:0 0 24px}.page-heading h1{font-size:22px;margin:0 0 4px}.page-heading .muted{font-size:13px}.workspace-filters{display:flex;gap:10px}.workspace-filters label{gap:4px;color:#4b6155;font-size:10px;font-weight:750;letter-spacing:.04em;text-transform:uppercase}.workspace-filters select{min-width:154px;min-height:36px;padding:7px 9px;border-color:#ccd8d0;font-size:12px;font-weight:500;letter-spacing:0;text-transform:none}.filter-bar{display:grid;grid-template-columns:repeat(3,minmax(0,1fr)) auto;gap:12px;align-items:end;margin-bottom:18px;padding:10px 0}.filter-bar label{font-size:10px;color:#405349;font-weight:750;letter-spacing:.06em;text-transform:uppercase}.filter-bar input,.filter-bar select{min-height:34px;padding:7px 9px;border-color:#ccd8d0;font-size:12px}.filter-summary{display:grid;gap:4px;align-content:center;min-height:34px;padding:6px 0}.filter-summary-label{color:#708077;font-size:10px;font-weight:750;letter-spacing:.04em;text-transform:uppercase}.filter-summary strong{font-size:12px;color:#183c2b}.filter-actions{display:flex;gap:8px}.filter-actions .button{min-height:34px;padding:7px 15px;font-size:12px}.table-workspace{border-radius:10px;overflow:hidden;background:#fff;border:1px solid #e0ebe5;box-shadow:0 6px 20px rgba(21,35,30,.045)}.table-workspace .table-wrap{border:0}.table-toolbar{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:18px 22px;border-bottom:1px solid #dceee4;background:#f2fbf6}.table-title{display:flex;align-items:center;gap:10px;min-width:0}.table-title h2{margin:0;font-size:14px}.table-count{display:inline-flex;align-items:center;min-height:23px;padding:2px 8px;border-radius:999px;background:#0a9f54;color:#fff;font-size:11px;font-weight:800;white-space:nowrap}.table-tools{display:flex;align-items:center;gap:8px}.table-search{width:min(290px,38vw);min-height:34px;padding:7px 10px;border:1px solid #cfdad4;border-radius:6px;background:#fff;font:inherit;font-size:12px}.data-table{font-size:12px}.data-table th{padding:13px 16px;background:#fff;color:#52645a;font-size:10px}.data-table td{padding:15px 16px}.data-table tbody tr{transition:background .15s ease}.data-table tbody tr:hover{background:#f7fcf9}.data-table .row-main{font-weight:750;color:#15231e}.data-table .row-detail{display:block;margin-top:2px;color:#708077;font-size:11px}.row-actions{display:flex;flex-wrap:wrap;gap:6px}.button.small{min-height:29px;padding:5px 9px;font-size:11px}.button.outline{background:#fff;color:var(--forest)}.button.danger{background:#c63f3a;border-color:#c63f3a}.empty-row td{text-align:center;color:var(--muted);padding:32px}.statistics-workspace,.queue-summary{padding:0;overflow:hidden}.statistics-head{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:20px 24px;border-bottom:1px solid #dceee4;background:#f2fbf6}.statistics-head h2{margin:0;font-size:15px}.statistics-head .muted{margin:3px 0 0;font-size:12px}.chart-legend{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:14px;padding-top:3px;color:#45594e;font-size:11px}.chart-legend span{display:inline-flex;align-items:center;gap:6px}.chart-legend i{width:11px;height:11px;border-radius:2px}.legend-students{background:#0a9f54}.legend-requests{background:#2563db}.legend-accounts{background:#d9469c}.campus-chart-scroll{overflow-x:auto;padding:16px 16px 2px}.campus-chart{display:block;min-width:750px;width:100%;height:auto}.chart-axis{stroke:#9caca4;stroke-width:1}.chart-gridline{stroke:#dfe7e2;stroke-width:1}.chart-tick,.chart-campus{fill:#496055;font:11px Inter,"Segoe UI",Arial,sans-serif}.chart-campus{font-size:10px}.queue-stat-list{display:grid;grid-template-columns:repeat(3,1fr)}.queue-stat-list a{display:grid;grid-template-columns:1fr auto;gap:2px 12px;padding:20px 24px;border-right:1px solid var(--line);color:var(--ink);text-decoration:none}.queue-stat-list a:last-child{border-right:0}.queue-stat-list a:hover{background:#f7fcf9}.queue-stat-list span{font-size:12px;font-weight:750}.queue-stat-list strong{grid-row:span 2;align-self:center;color:#087b43;font-size:30px;line-height:1}.queue-stat-list small{color:var(--muted);font-size:11px}.table-form{display:flex;flex-wrap:wrap;gap:6px;min-width:220px}.table-form select,.table-form input{width:auto;min-height:30px;padding:4px 7px;font-size:11px}.table-form input{min-width:112px}@media(max-width:850px){.page-heading{flex-direction:column}.workspace-filters{width:100%}.workspace-filters label{flex:1}.workspace-filters select{min-width:0}.filter-bar{grid-template-columns:repeat(2,minmax(0,1fr))}.filter-actions{grid-column:1/-1}.table-toolbar{align-items:flex-start;flex-direction:column}.table-search{width:100%}.statistics-head{flex-direction:column}.chart-legend{justify-content:flex-start}.queue-stat-list{grid-template-columns:1fr}.queue-stat-list a{border-right:0;border-bottom:1px solid var(--line)}.queue-stat-list a:last-child{border-bottom:0}}@media(max-width:520px){.workspace-filters{flex-direction:column}.filter-bar{grid-template-columns:1fr;padding-inline:0}.table-tools{width:100%}}
    </style>
    <style>
        .filter-bar{display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px;margin-bottom:18px;padding:10px 24px;border:1px solid #e8f0eb;border-radius:15px;box-shadow:0 5px 13px rgba(21,35,30,.09)}.filter-bar label{flex:1 1 190px;gap:4px;color:#263a30;font-size:10px;font-weight:650;letter-spacing:0;text-transform:none}.filter-bar input,.filter-bar select{min-height:30px;padding:6px 10px;border-color:#cbd7d0;border-radius:7px;font-size:12px}.filter-summary{flex:0 1 150px;min-height:30px;padding:3px 0}.filter-summary-label{font-size:10px;font-weight:650;letter-spacing:0;text-transform:none}.filter-summary strong{font-size:12px}.filter-actions{display:flex;align-items:flex-end;gap:8px}.filter-actions .button{min-width:100px;min-height:32px;padding:7px 15px;border-radius:7px;font-size:12px}.filter-actions .button.secondary{border-color:#6e798c;background:#6e798c;color:#fff}.filter-actions .button.secondary:hover{border-color:#5a6475;background:#5a6475}@media(max-width:850px){.filter-bar{padding:13px 18px}.filter-bar label{flex-basis:calc(50% - 10px)}.filter-summary{flex:1 1 140px}.filter-actions{flex:1 1 100%;justify-content:flex-start}}@media(max-width:520px){.filter-bar{padding:13px 16px}.filter-bar label,.filter-summary,.filter-actions{flex-basis:100%}.filter-actions .button{flex:1}}
    </style>
    <style>
        .public-form-page{width:min(640px,100%);margin-inline:auto}.repair-form-page{width:min(980px,100%)}.auth-form-page{margin-top:28px}.inline-request-form{margin:20px auto 0}.centered-form{justify-items:center}.centered-form label{width:100%;text-align:left}.remember-me{display:flex;align-items:center;gap:8px;font-weight:600}.remember-me input{width:auto;min-height:auto}.field-optional{color:var(--muted);font-size:11px;font-weight:500}.form-actions{display:flex;justify-content:flex-end;margin-top:2px}.registration-directory{width:min(760px,100%);margin-inline:auto}.registration-directory-heading{text-align:center;margin-bottom:22px}.registration-directory-heading h1{margin-bottom:6px}.registration-directory-heading p{margin:0}.registration-link-list{display:grid;gap:10px}.registration-link{display:grid;grid-template-columns:1fr auto;gap:3px 16px;align-items:center;padding:16px 18px;border:1px solid #cfe1d6;border-radius:7px;background:#f8fcf9;color:var(--ink);text-decoration:none}.registration-link:hover,.registration-link:focus-visible{border-color:#58a87b;background:var(--mint);outline:0}.registration-link-campus{font-size:14px;font-weight:800}.registration-link-detail{color:var(--muted);font-size:12px}.registration-link-action{grid-column:2;grid-row:1 / span 2;color:var(--forest);font-size:12px;font-weight:800}.registration-empty{padding:26px 20px;text-align:center;border:1px dashed #c7d8ce;border-radius:7px;background:#f8fcf9}.registration-empty h2{margin-bottom:5px;font-size:16px}.registration-empty p{margin:0}.registration-modal{width:min(980px,calc(100% - 40px))}.registration-form-grid{grid-template-columns:repeat(12,minmax(0,1fr));gap:18px}.registration-form-grid label{grid-column:span 4}.registration-form-grid .registration-field-wide{grid-column:span 6}.registration-form-grid .registration-field-narrow{grid-column:span 3}.registration-form-grid .full{grid-column:1/-1}.swal2-popup.ict-toast{width:min(320px,calc(100vw - 24px));padding:10px 14px;border-radius:8px;font-size:12px;box-shadow:0 10px 28px rgba(15,35,26,.18)}.swal2-popup.ict-toast .swal2-title{margin:0 0 2px;font-size:13px}.swal2-popup.ict-toast .swal2-html-container{margin:0;font-size:12px;line-height:1.35}.swal2-popup.ict-toast .swal2-icon{width:22px;height:22px;margin:0 9px 0 0;border-width:2px}.swal2-popup.ict-toast .swal2-icon .swal2-icon-content{font-size:15px}.swal2-popup.ict-toast .swal2-close{display:flex;align-items:center;justify-content:center;line-height:1}@media(max-width:900px){.registration-form-grid label,.registration-form-grid .registration-field-wide,.registration-form-grid .registration-field-narrow{grid-column:span 6}}@media(max-width:520px){.form-actions .button{width:100%}.registration-link{padding:14px}.registration-link-campus{font-size:13px}.registration-modal{width:calc(100% - 20px)}.registration-form-grid label,.registration-form-grid .registration-field-wide,.registration-form-grid .registration-field-narrow{grid-column:1/-1}.swal2-popup.ict-toast{width:calc(100vw - 20px)}}
    </style>
    <style>
        body{overflow-x:hidden}.landing-main{width:100%;margin:0}.site-header{position:sticky;top:0;z-index:50}.is-landing-page .site-header{position:absolute;inset:0 0 auto;color:#fff}.is-landing-page .brand,.is-landing-page .site-nav a{color:#fff}.is-landing-page .brand-copy small{color:#d4f2df}.is-landing-page .site-nav a:hover,.is-landing-page .site-nav a:focus-visible{background:rgba(255,255,255,.14)}.is-landing-page .site-nav .nav-action{background:rgba(255,255,255,.16);border-color:rgba(255,255,255,.48);color:#fff}.is-landing-page .site-nav .nav-action:hover{background:rgba(255,255,255,.26)}.is-landing-page .account-menu summary{border-color:rgba(255,255,255,.4);color:#fff}.is-landing-page .profile-avatar{background:#fff;color:var(--forest-dark)}
    </style>
    <style>
        .site-header{background:var(--forest);border-bottom-color:var(--forest-dark);box-shadow:0 5px 18px rgba(3,42,23,.18);color:#fff}
        .site-header .brand,.site-header .site-nav a,.site-header .account-menu summary{color:#fff}
        .site-header .brand-copy small{color:#d4f2df}
        .site-header .site-nav a:hover,.site-header .site-nav a:focus-visible{background:rgba(255,255,255,.14)}
        .site-header .site-nav .nav-action{background:rgba(255,255,255,.16);border-color:rgba(255,255,255,.48);color:#fff}
        .site-header .site-nav .nav-action:hover{background:rgba(255,255,255,.26);border-color:rgba(255,255,255,.7)}
        .site-header .account-menu summary{border-color:rgba(255,255,255,.4)}
        .site-header .profile-avatar{background:#fff;color:var(--forest-dark)}
        .is-landing-page .site-header{background:transparent;border-bottom-color:transparent;box-shadow:none}
    </style>
    <style>
        /* Responsive layout refinements shared by public and staff pages. */
        html{overflow-x:hidden}
        img,svg{max-width:100%}
        button,input,select,textarea{max-width:100%}
        .wrap{min-width:0}
        .page-heading>div{min-width:0}
        .page-heading h1{overflow-wrap:anywhere}
        .panel{min-width:0}
        .table-workspace{min-width:0}
        .nav-toggle,.mobile-nav-close,.mobile-nav-backdrop{display:none}
        .icon-sprite{position:absolute;width:0;height:0;overflow:hidden}
        .nav-icon{width:15px;height:15px;flex:0 0 15px;fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-width:1.8}
        .site-nav a{display:inline-flex;align-items:center;gap:6px}

        @media (max-width:900px){
            :root{--page-container-width:min(1320px,calc(100% - 28px))}
            .nav-shell{gap:10px}
            .wrap{margin-bottom:32px}
            .panel{padding:20px}
            .statistics-head{padding:18px 20px}
            .queue-stat-list a{padding:18px 20px}
        }

        @media (max-width:640px){
            :root{--page-container-width:calc(100% - 24px)}
            .nav-shell{align-items:center;padding:7px 0}
            .brand{min-width:0}
            .brand-copy strong{font-size:11px}
            .nav-toggle{display:inline-grid;place-items:center;width:38px;height:34px;margin:0;padding:0;border:1px solid rgba(255,255,255,.45);border-radius:7px;background:rgba(255,255,255,.12);color:inherit;font-size:21px;line-height:1;cursor:pointer}
            .site-nav{position:fixed;z-index:100;inset:0 auto 0 0;display:flex;width:min(290px,86vw);height:100dvh;flex-direction:column;align-items:stretch;gap:5px;padding:76px 16px 20px;overflow-y:auto;background:var(--forest-dark);box-shadow:14px 0 35px rgba(0,0,0,.22);transform:translateX(-105%);transition:transform .22s ease;visibility:hidden}
            .site-nav.is-open{transform:translateX(0);visibility:visible}
            .site-nav::before{content:"Menu";position:absolute;top:22px;left:16px;color:#fff;font-size:17px;font-weight:800;letter-spacing:.02em}
            .site-nav a{display:flex;align-items:center;min-height:44px;flex:0 0 auto;padding:10px 12px;border-radius:7px;color:#fff;font-size:13px}
            .site-nav a:hover,.site-nav a:focus-visible{background:rgba(255,255,255,.14)}
            .site-nav .nav-action{justify-content:center;margin-top:8px;border-color:rgba(255,255,255,.5);background:rgba(255,255,255,.16)}
            .mobile-nav-close{position:absolute;top:17px;right:14px;display:grid;place-items:center;width:34px;height:34px;padding:0;border:0;border-radius:50%;background:rgba(255,255,255,.12);color:#fff;font-size:24px;line-height:1;cursor:pointer}
            .mobile-nav-backdrop{display:block;position:fixed;z-index:90;inset:0;background:rgba(4,31,20,.48);opacity:0;pointer-events:none;transition:opacity .22s ease}
            body.nav-is-open{overflow:hidden}
            body.nav-is-open .mobile-nav-backdrop{opacity:1;pointer-events:auto}
            .account-menu{margin-left:auto}
            .account-menu-panel{right:-2px}
            .wrap{margin-top:16px}
            .page-heading{gap:12px;margin-bottom:18px}
            .page-heading h1{font-size:20px}
            .workspace-filters{gap:8px}
            .filter-bar{gap:10px;margin-bottom:14px;padding:14px}
            .filter-bar label,.filter-summary,.filter-actions{flex-basis:100%}
            .filter-actions{display:grid;grid-template-columns:1fr 1fr}
            .filter-actions .button{min-width:0;width:100%}
            .panel{padding:16px}
            .statistics-head{padding:16px}
            .statistics-head h2{font-size:14px}
            .chart-legend{gap:8px 12px}
            .campus-chart-scroll{margin-inline:-4px;padding-inline:4px}
            .table-toolbar{padding:14px 16px;gap:12px}
            .table-title{align-items:flex-start;flex-wrap:wrap}
            .table-tools{width:100%}
            .table-search{width:100%;max-width:none}
            .table-wrap{margin-inline:0}
            .data-table{min-width:650px}
            .data-table th,.data-table td{padding:11px 12px}
            .modal{max-height:calc(100dvh - 20px);border-radius:8px}
            .modal-header{padding-top:16px}
            .modal-footer{display:grid;grid-template-columns:1fr 1fr}
            .modal-footer .button{width:100%}
            .homepage-track{max-width:none}
            .form-actions{display:grid;grid-template-columns:1fr}
        }

        @media (max-width:380px){
            .brand img{width:28px;height:28px}
            .brand-copy strong{font-size:10px}
            .nav-toggle{width:34px;height:32px}
            .site-nav{padding-inline:12px}
            .panel{padding:14px}
            .filter-bar{padding:12px}
            .statistics-head{padding:14px}
            .queue-stat-list a{padding:16px}
            .queue-stat-list strong{font-size:26px}
            .modal-footer{grid-template-columns:1fr}
        }

        @media (min-width:901px) and (max-width:1100px){
            .nav-shell{width:calc(100% - 28px)}
            .site-nav a{padding-inline:7px}
            .profile-name{max-width:90px}
        }



        @media (min-width:1440px){
            .wrap{margin-top:32px}
            .panel{padding:28px}
        }
    </style>
</head>
<?php
$serverAlerts = [];
if ($success = session('success')) {
    $serverAlerts[] = ['icon' => 'success', 'title' => 'Success', 'text' => (string) $success];
}
if ($error = session('error')) {
    $serverAlerts[] = ['icon' => 'error', 'title' => 'Unable to continue', 'text' => (string) $error];
}
$validationErrors = session('errors');
if ($validationErrors) {
    $validationMessage = is_array($validationErrors)
        ? implode("\n", array_filter(array_map(static fn ($message): string => is_scalar($message) ? (string) $message : '', $validationErrors)))
        : (string) $validationErrors;
    $serverAlerts[] = ['icon' => 'error', 'title' => 'Please check the form', 'text' => $validationMessage];
}
?>
<body class="<?= ! empty($isLandingPage) ? 'is-landing-page' : '' ?>" data-validation-errors="<?= $validationErrors ? 'true' : 'false' ?>">
<svg class="icon-sprite" aria-hidden="true" focusable="false">
    <symbol id="icon-home" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z"/></symbol>
    <symbol id="icon-request" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/><rect x="4" y="4" width="16" height="16" rx="3"/></symbol>
    <symbol id="icon-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></symbol>
    <symbol id="icon-card" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h4"/></symbol>
    <symbol id="icon-accounts" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/><circle cx="7" cy="6" r="1"/><circle cx="7" cy="12" r="1"/><circle cx="7" cy="18" r="1"/></symbol>
    <symbol id="icon-reset" viewBox="0 0 24 24"><path d="M4 10a8 8 0 1 1 2 7"/><path d="M4 4v6h6"/></symbol>
    <symbol id="icon-repair" viewBox="0 0 24 24"><path d="m14 6 4-3 3 3-3 4"/><path d="m13 11-8 8a2 2 0 0 0 3 3l8-8"/><path d="m16 8-5 5"/></symbol>
    <symbol id="icon-replace" viewBox="0 0 24 24"><path d="M4 7h13l-3-3M20 17H7l3 3"/><path d="M17 7a6 6 0 0 1 3 5M7 17a6 6 0 0 1-3-5"/></symbol>
    <symbol id="icon-link" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7 0l2-2a5 5 0 0 0-7-7l-1 1M14 11a5 5 0 0 0-7 0l-2 2a5 5 0 0 0 7 7l1-1"/></symbol>
    <symbol id="icon-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0 1 12 0M16 5a3 3 0 0 1 0 6M17 14a5 5 0 0 1 4 6"/></symbol>
</svg>
<header class="site-header">
    <div class="nav-shell">
        <button class="nav-toggle" type="button" aria-label="Open navigation menu" aria-controls="site-navigation" aria-expanded="false">&#9776;</button>
        <a class="brand" href="<?= site_url('/') ?>">
            <img src="<?= base_url('sksu.png') ?>" alt="SKSU logo">
            <span class="brand-copy"><strong>SKSU-IOMS</strong><small>Service Portal</small></span>
        </a>
        <nav id="site-navigation" class="site-nav" aria-label="Main navigation">
            <button class="mobile-nav-close" type="button" aria-label="Close navigation menu">&times;</button>
            <?php if (auth()->loggedIn()): ?>
                <a href="<?= site_url('staff') ?>"><svg class="nav-icon"><use href="#icon-dashboard"></use></svg><span>Overview</span></a>
                <?php if (staff_can('admin', 'ict', 'registrar')): ?><a href="<?= site_url('staff/ids') ?>"><svg class="nav-icon"><use href="#icon-card"></use></svg><span>ID queue</span></a><?php endif ?>
                <?php if (staff_can('admin', 'ict')): ?><a href="<?= site_url('staff/accounts') ?>"><svg class="nav-icon"><use href="#icon-accounts"></use></svg><span>Accounts</span></a><a href="<?= site_url('staff/resets') ?>"><svg class="nav-icon"><use href="#icon-reset"></use></svg><span>Resets</span></a><a href="<?= site_url('staff/repairs') ?>"><svg class="nav-icon"><use href="#icon-repair"></use></svg><span>Repairs</span></a><?php endif ?>
                <?php if (staff_can('admin', 'ict', 'cashier')): ?><a href="<?= site_url('staff/replacements') ?>"><svg class="nav-icon"><use href="#icon-replace"></use></svg><span>Replacements</span></a><?php endif ?>
                <?php if (staff_can('admin')): ?><a href="<?= site_url('staff/links') ?>"><svg class="nav-icon"><use href="#icon-link"></use></svg><span>Registration links</span></a><a href="<?= site_url('staff/users') ?>"><svg class="nav-icon"><use href="#icon-users"></use></svg><span>Staff users</span></a><?php endif ?>
            <?php else: ?>
                <a href="<?= site_url('/') ?>"><svg class="nav-icon"><use href="#icon-home"></use></svg><span>Home</span></a><a href="<?= site_url('id') ?>"><svg class="nav-icon"><use href="#icon-request"></use></svg><span>Submit Request</span></a><a class="nav-action" href="<?= site_url('login') ?>"><svg class="nav-icon"><use href="#icon-users"></use></svg><span>Staff sign in</span></a>
            <?php endif ?>
        </nav>
        <?php if (auth()->loggedIn()): $headerUser = auth()->user(); $displayName = (string) ($headerUser->username ?? 'Account'); ?>
            <details class="account-menu">
                <summary aria-label="Open account menu"><span class="profile-avatar"><?= esc(strtoupper(substr($displayName, 0, 1))) ?></span><span class="profile-name"><?= esc($displayName) ?></span><span class="profile-caret" aria-hidden="true">⌄</span></summary>
                <div class="account-menu-panel">
                    <p><span class="account-caption">Signed in as</span><strong><?= esc($displayName) ?></strong></p>
                    <?php if (staff_can('admin')): ?><a href="<?= site_url('staff/settings') ?>">Settings</a><?php endif ?>
                    <a href="<?= site_url('logout') ?>">Sign out</a>
                </div>
            </details>
        <?php endif ?>
    </div>
    <button class="mobile-nav-backdrop" type="button" aria-label="Close navigation menu"></button>
</header>
<main class="<?= ! empty($isLandingPage) ? 'landing-main' : 'wrap' ?>">
    <?= $this->renderSection('content') ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
const serverAlerts = <?= json_encode($serverAlerts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const feedbackAlerts = [
    ...serverAlerts,
    ...[...document.querySelectorAll('[data-swal-alert]')].map((alert) => ({
        icon: alert.dataset.swalIcon || 'info',
        title: alert.dataset.swalTitle || '',
        text: alert.dataset.swalText || '',
    })),
];

window.ictNotify = (options) => window.Swal
    ? Swal.fire({
        ...options,
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        customClass: { popup: 'ict-toast' },
    })
    : Promise.resolve();

async function showFeedbackAlerts() {
    for (const alert of feedbackAlerts) {
        await window.ictNotify(alert);
    }
    if (document.body.dataset.validationErrors === 'true') {
        document.querySelector('[data-open-on-error]')?.showModal();
    }
}

showFeedbackAlerts();

const navToggle = document.querySelector('.nav-toggle');
const siteNavigation = document.querySelector('#site-navigation');
const navClose = document.querySelector('.mobile-nav-close');
const navBackdrop = document.querySelector('.mobile-nav-backdrop');
function setMobileNavigation(open) {
    if (!siteNavigation || !navToggle) return;
    siteNavigation.classList.toggle('is-open', open);
    document.body.classList.toggle('nav-is-open', open);
    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
}
navToggle?.addEventListener('click', () => setMobileNavigation(true));
navClose?.addEventListener('click', () => setMobileNavigation(false));
navBackdrop?.addEventListener('click', () => setMobileNavigation(false));
siteNavigation?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setMobileNavigation(false));
});
window.addEventListener('resize', () => {
    if (window.innerWidth > 640) setMobileNavigation(false);
});

const openModalId = <?= json_encode(session('open_modal'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
if (openModalId) document.getElementById(openModalId)?.showModal();

function applyTableFilters(table) {
    const search = document.querySelector(`[data-table-search="${table.id}"]`)?.value.toLowerCase().trim() ?? '';
    const filters = [...document.querySelectorAll(`[data-table-filter="${table.id}"]`)];
    let visible = 0;

    table.querySelectorAll('tbody tr').forEach((row) => {
        if (row.classList.contains('empty-row')) return;
        const matchesSearch = search === '' || row.textContent.toLowerCase().includes(search);
        const matchesFilters = filters.every((select) => {
            if (select.value === '') return true;
            const cell = row.cells[Number(select.dataset.column)];
            const cellValue = cell?.textContent.toLowerCase().trim() ?? '';
            const filterValue = select.value.toLowerCase();
            return select.dataset.exact === 'true' ? cellValue === filterValue : cellValue.includes(filterValue);
        });
        row.hidden = !matchesSearch || !matchesFilters;
        if (!row.hidden) visible++;
    });

    document.querySelectorAll(`[data-filter-count="${table.id}"]`).forEach((count) => { count.textContent = `${visible} shown`; });
    document.querySelectorAll(`[data-table-count="${table.id}"]`).forEach((count) => { count.textContent = `${visible} shown`; });
}

document.querySelectorAll('[data-table-search]').forEach((input) => {
    const table = document.getElementById(input.dataset.tableSearch);
    if (table) input.addEventListener('input', () => applyTableFilters(table));
});

document.querySelectorAll('[data-table-filter]').forEach((select) => {
    const table = document.getElementById(select.dataset.tableFilter);
    if (table) select.addEventListener('change', () => applyTableFilters(table));
});

document.querySelectorAll('[data-apply-filters]').forEach((button) => {
    button.addEventListener('click', () => {
        const table = document.getElementById(button.dataset.applyFilters);
        if (table) applyTableFilters(table);
    });
});

document.querySelectorAll('[data-reset-filters]').forEach((button) => {
    button.addEventListener('click', () => {
        const table = document.getElementById(button.dataset.resetFilters);
        if (!table) return;
        document.querySelectorAll(`[data-table-filter="${table.id}"]`).forEach((filter) => { filter.value = ''; });
        const search = document.querySelector(`[data-table-search="${table.id}"]`);
        if (search) search.value = '';
        applyTableFilters(table);
    });
});

document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
        const modal = document.getElementById(trigger.dataset.modalOpen);
        if (modal && !modal.open) modal.showModal();
    });
});
document.querySelectorAll('[data-modal-close]').forEach((trigger) => {
    trigger.addEventListener('click', () => trigger.closest('dialog')?.close());
});
document.querySelectorAll('dialog.modal').forEach((modal) => {
    modal.addEventListener('click', (event) => { if (event.target === modal) modal.close(); });
});
document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        if (form.dataset.confirmed === 'true') return;
        event.preventDefault();
        const message = form.dataset.confirm || 'Are you sure you want to continue?';
        const confirmed = window.Swal
            ? (await Swal.fire({ title: 'Confirm action', text: message, icon: 'warning', showCancelButton: true, confirmButtonText: 'Continue', confirmButtonColor: '#c63f3a' })).isConfirmed
            : window.confirm(message);
        if (confirmed) { form.dataset.confirmed = 'true'; form.requestSubmit(); }
    });
});
const campusChartFilter = document.querySelector('[data-campus-chart-filter]');
const seriesChartFilter = document.querySelector('[data-series-chart-filter]');
function applyCampusChartFilters() {
    const campus = campusChartFilter?.value ?? '';
    const series = seriesChartFilter?.value ?? '';
    document.querySelectorAll('[data-campus-chart-group]').forEach((group) => {
        group.style.display = campus === '' || group.dataset.campusIndex === campus ? '' : 'none';
        group.querySelectorAll('[data-chart-series]').forEach((bar) => {
            bar.style.display = series === '' || bar.dataset.chartSeries === series ? '' : 'none';
        });
    });
}
campusChartFilter?.addEventListener('change', applyCampusChartFilters);
seriesChartFilter?.addEventListener('change', applyCampusChartFilters);

</script>
</body>
</html>
