const $ = (s, root=document) => root.querySelector(s);
const $$ = (s, root=document) => [...root.querySelectorAll(s)];

const defaultData = {
  orders: [
    {id:'1048', client:'Nadia A.', piece:'Robe cérémonie', stage:'Essayage', progress:82, due:'30 sept.', price:95000, cost:61000},
    {id:'1047', client:'M. Yao', piece:'Ensemble homme', stage:'Tissu validé', progress:34, due:'03 oct.', price:120000, cost:76000},
    {id:'1044', client:'Prisca K.', piece:'Deux-pièces', stage:'Couture', progress:61, due:'01 oct.', price:85000, cost:54000},
    {id:'1042', client:'Aïcha S.', piece:'Robe Akpè', stage:'Prêt', progress:100, due:'Aujourd’hui', price:78000, cost:47000}
  ],
  clients: [
    {name:'Nadia A.', phone:'+229 97 00 11 22', profiles:2, last:'18 sept.'},
    {name:'M. Yao', phone:'+229 96 10 42 18', profiles:1, last:'17 sept.'},
    {name:'Prisca K.', phone:'+229 94 51 33 02', profiles:1, last:'15 sept.'},
    {name:'Aïcha S.', phone:'+229 98 11 72 04', profiles:3, last:'13 sept.'}
  ],
  stock: [
    {name:'Wax premium bleu', category:'Tissu', qty:18, unit:'m', min:8},
    {name:'Lin naturel', category:'Tissu', qty:5, unit:'m', min:10},
    {name:'Fermeture invisible', category:'Mercerie', qty:22, unit:'pcs', min:12},
    {name:'Boutons bois', category:'Mercerie', qty:9, unit:'pcs', min:15}
  ],
  appointments: [
    {time:'09:30', client:'Nadia A.', type:'Essayage', piece:'Robe cérémonie'},
    {time:'14:00', client:'M. Yao', type:'Choix tissu', piece:'Ensemble homme'},
    {time:'16:30', client:'Aïcha S.', type:'Livraison', piece:'Robe Akpè'}
  ],
  clientOrders: [
    {id:'1048', atelier:'Maison Afi', piece:'Robe cérémonie', stage:'Essayage', progress:82, date:'30 sept.', amount:95000},
    {id:'1031', atelier:'Maison Afi', piece:'Chemise brodée', stage:'Prêt', progress:100, date:'12 sept.', amount:42000}
  ]
};

let data = JSON.parse(localStorage.getItem('atelierData') || 'null') || defaultData;
let currentRole = localStorage.getItem('atelierRole') || '';
let currentView = 'overview';

function save(){ localStorage.setItem('atelierData', JSON.stringify(data)); }
function toast(msg){ const el=$('#toast'); el.textContent=msg; el.classList.remove('hidden'); setTimeout(()=>el.classList.add('hidden'),2400); }
function money(n){ return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA'; }
function stageClass(stage){ return stage.toLowerCase().replaceAll('é','e').replaceAll('û','u').replace(/\s+/g,'-'); }

function openAuth(){ $('#authModal').classList.remove('hidden'); $('#authModal').setAttribute('aria-hidden','false'); }
function closeAuth(){ $('#authModal').classList.add('hidden'); $('#authModal').setAttribute('aria-hidden','true'); }
function enterRole(role){ currentRole=role; localStorage.setItem('atelierRole', role); closeAuth(); showApp(); }
function showApp(){
  $('#landing').classList.add('hidden'); $('.topbar').classList.add('hidden'); $('#app').classList.remove('hidden'); $('#app').setAttribute('aria-hidden','false');
  currentView = roleViews[currentRole][0].id; renderApp();
}
function exitApp(){ $('#app').classList.add('hidden'); $('#app').setAttribute('aria-hidden','true'); $('#landing').classList.remove('hidden'); $('.topbar').classList.remove('hidden'); currentRole=''; localStorage.removeItem('atelierRole'); window.scrollTo({top:0,behavior:'smooth'}); }

const roleViews = {
  stylist: [
    {id:'overview', label:'Vue d’ensemble', icon:'⌂'},
    {id:'orders', label:'Commandes', icon:'◫'},
    {id:'clients', label:'Clients & mesures', icon:'◎'},
    {id:'production', label:'Production', icon:'◌'},
    {id:'stock', label:'Stock', icon:'□'},
    {id:'planning', label:'Planning', icon:'◷'}
  ],
  client: [
    {id:'client-home', label:'Mon espace', icon:'⌂'},
    {id:'client-orders', label:'Mes commandes', icon:'◫'},
    {id:'measurements', label:'Mes mesures', icon:'◎'},
    {id:'atelier-discovery', label:'Trouver un atelier', icon:'⌕'}
  ],
  admin: [
    {id:'admin-overview', label:'Vue globale', icon:'⌂'},
    {id:'admin-stylists', label:'Stylistes', icon:'◎'},
    {id:'admin-orders', label:'Commandes', icon:'◫'},
    {id:'admin-moderation', label:'Modération', icon:'✓'}
  ]
};

function renderNav(){
  const nav=$('#appNav'); nav.innerHTML='';
  roleViews[currentRole].forEach(v=>{
    const b=document.createElement('button'); b.className='app-nav-item'+(v.id===currentView?' active':''); b.innerHTML=`<span>${v.icon}</span>${v.label}`; b.onclick=()=>{currentView=v.id; renderApp()}; nav.appendChild(b);
  });
}
function renderApp(){
  renderNav();
  const titles = {overview:'Votre atelier en un coup d’œil.',orders:'Commandes',clients:'Clients & mesures',production:'Production',stock:'Stock & matières',planning:'Planning', 'client-home':'Bonjour, Nadia.','client-orders':'Mes commandes','measurements':'Mes profils de mesures','atelier-discovery':'Ateliers à découvrir','admin-overview':'Supervision de la plateforme','admin-stylists':'Comptes stylistes','admin-orders':'Suivi des commandes','admin-moderation':'Modération'};
  $('#appTitle').textContent=titles[currentView] || 'Atelier';
  $('#appEyebrow').textContent=currentRole==='stylist'?'ESPACE STYLISTE':currentRole==='client'?'ESPACE CLIENT':'ADMINISTRATION';
  $('#roleChip').textContent=currentRole==='stylist'?'STYLISTE':currentRole==='client'?'CLIENT':'ADMIN';
  $('#profileBtn').textContent=currentRole==='stylist'?'Afi · Styliste':currentRole==='client'?'Nadia · Cliente':'Admin · Atelier';
  $('#appContent').innerHTML=renderView();
  bindView();
}

function renderView(){
  if(currentRole==='stylist') return renderStylist();
  if(currentRole==='client') return renderClient();
  return renderAdmin();
}
function renderStat(label,value,note){return `<div class="app-stat"><span>${label}</span><strong>${value}</strong><small>${note}</small></div>`}
function renderStylist(){
  switch(currentView){
    case 'orders': return `<div class="content-toolbar"><div><h3>Vos commandes</h3><p>Centralisez les demandes, acomptes et dates de livraison.</p></div><button class="primary small" id="newOrder">+ Nouvelle commande</button></div><div class="table-card"><div class="table-head"><span>Commande</span><span>Client</span><span>Étape</span><span>Échéance</span><span>Montant</span></div>${data.orders.map((o,i)=>`<div class="table-row"><div><strong>#${o.id}</strong><small>${o.piece}</small></div><span>${o.client}</span><span><b class="status ${stageClass(o.stage)}">${o.stage}</b></span><span>${o.due}</span><span>${money(o.price)}</span></div>`).join('')}</div>`;
    case 'clients': return `<div class="content-toolbar"><div><h3>Carnet clients</h3><p>Mensurations historisées, profils multiples et notes d’atelier.</p></div><button class="secondary small" id="newClient">+ Ajouter un client</button></div><div class="client-grid">${data.clients.map(c=>`<article class="mini-card"><div class="avatar">${c.name.split(' ').map(x=>x[0]).join('').slice(0,2)}</div><div><strong>${c.name}</strong><small>${c.phone}</small><span>${c.profiles} profil${c.profiles>1?'s':''} · dernière mise à jour ${c.last}</span></div><button class="ghost-btn" data-client="${c.name}">Voir</button></article>`).join('')}</div>`;
    case 'production': return `<div class="content-toolbar"><div><h3>Flux de production</h3><p>Faites avancer chaque pièce étape par étape.</p></div><button class="secondary small" id="advanceAll">Simuler l’avancement</button></div><div class="kanban">${['Reçue','Tissu validé','Coupe','Couture','Essayage','Prêt'].map(s=>`<div class="kanban-col"><header><strong>${s}</strong><span>${data.orders.filter(o=>o.stage===s).length}</span></header>${data.orders.filter(o=>o.stage===s).map(o=>`<article class="kanban-card"><span>#${o.id}</span><strong>${o.piece}</strong><small>${o.client}</small><div class="progress"><i style="width:${o.progress}%"></i></div><small>${o.progress}%</small></article>`).join('') || '<em class="empty">Aucune pièce</em>'}</div>`).join('')}</div>`;
    case 'stock': return `<div class="content-toolbar"><div><h3>Stock & matières</h3><p>Surveillez les seuils critiques avant qu’une commande ne bloque.</p></div><button class="secondary small" id="addStock">+ Ajouter matière</button></div><div class="stock-table">${data.stock.map((s,i)=>`<div class="stock-row"><div><strong>${s.name}</strong><small>${s.category}</small></div><span>${s.qty} ${s.unit}</span><span class="stock-meter"><i style="width:${Math.min(100,(s.qty/Math.max(s.min*2,s.qty))*100)}%"></i></span><b class="stock-state ${s.qty<s.min?'danger':'ok'}">${s.qty<s.min?'Seuil critique':'Disponible'}</b></div>`).join('')}</div>`;
    case 'planning': return `<div class="content-toolbar"><div><h3>Planning du jour</h3><p>Essayages, choix de tissus et livraisons.</p></div><button class="primary small" id="newAppt">+ Rendez-vous</button></div><div class="agenda">${data.appointments.map(a=>`<article><time>${a.time}</time><div><strong>${a.type}</strong><span>${a.client} · ${a.piece}</span></div><button class="ghost-btn">Ouvrir</button></article>`).join('')}</div>`;
    default:
      const revenue=data.orders.reduce((s,o)=>s+o.price,0), margin=Math.round(data.orders.reduce((s,o)=>s+(o.price-o.cost),0)/revenue*100);
      return `<div class="welcome-strip"><div><span class="eyebrow">AUJOURD’HUI · COTONOU</span><h3>Votre atelier avance bien.</h3><p>Voici les éléments qui méritent votre attention.</p></div><div class="date-card"><strong>28</strong><span>SEPT. 2026</span></div></div><div class="app-stats">${renderStat('Commandes actives',data.orders.filter(o=>o.stage!=='Prêt').length,'hors livraisons')}${renderStat('Chiffre d’affaires',money(revenue),'sur le portefeuille démo')}${renderStat('Marge moyenne',margin+'%','coût matière + main-d’œuvre')}${renderStat('Alertes stock',data.stock.filter(s=>s.qty<s.min).length,'matières sous le seuil')}</div><div class="two-col"><div class="panel"><div class="panel-title"><h3>Production en cours</h3><button class="ghost-btn" data-nav="production">Voir tout ↗</button></div>${data.orders.filter(o=>o.stage!=='Prêt').map(o=>`<div class="prod-item"><div><strong>#${o.id} · ${o.piece}</strong><small>${o.client} · ${o.stage}</small></div><div class="prod-progress"><div class="progress"><i style="width:${o.progress}%"></i></div><b>${o.progress}%</b></div></div>`).join('')}</div><div class="panel"><div class="panel-title"><h3>À ne pas oublier</h3></div><div class="attention"><span>!</span><div><strong>2 matières sous le seuil</strong><small>Lin naturel · Boutons bois</small></div></div><div class="attention"><span>◷</span><div><strong>3 rendez-vous aujourd’hui</strong><small>Premier : 09:30 avec Nadia</small></div></div><div class="attention"><span>↗</span><div><strong>1 livraison prête</strong><small>Commande #1042</small></div></div></div></div>`;
  }
}

function renderClient(){
  switch(currentView){
    case 'client-orders': return `<div class="content-toolbar"><div><h3>Mes commandes</h3><p>Suivez la création de vos pièces sans appeler l’atelier.</p></div><button class="primary small" id="discoverBtn">Découvrir des pièces</button></div><div class="client-orders">${data.clientOrders.map(o=>`<article class="client-order"><div class="client-order-head"><div><span>#${o.id}</span><h4>${o.piece}</h4><small>${o.atelier}</small></div><b>${money(o.amount)}</b></div><div class="timeline"><span class="done"></span><span class="done"></span><span class="current"></span><span></span><span></span><span></span></div><div class="client-order-foot"><strong>${o.stage}</strong><span>Livraison estimée · ${o.date}</span><b>${o.progress}%</b></div></article>`).join('')}</div>`;
    case 'measurements': return `<div class="content-toolbar"><div><h3>Mes profils</h3><p>Conservez vos mensurations et celles de vos proches, avec historique.</p></div><button class="primary small" id="newProfile">+ Nouveau profil</button></div><div class="measurement-grid">${[['Moi · Nadia','Mis à jour le 18 sept.','Poitrine 92 · Taille 74 · Hanches 98'],['Maman','Mis à jour le 02 août','Poitrine 96 · Taille 82 · Hanches 104'],['Enfant · Lina','Mis à jour le 21 juin','Tour taille 58 · Longueur robe 76']].map((m,i)=>`<article><span>0${i+1}</span><h4>${m[0]}</h4><small>${m[1]}</small><p>${m[2]}</p><button class="ghost-btn">Modifier</button></article>`).join('')}</div>`;
    case 'atelier-discovery': return `<div class="content-toolbar"><div><h3>Trouvez votre atelier</h3><p>Une sélection de maisons et créateurs à proximité.</p></div></div><div class="atelier-grid-cards"><article><div class="mini-fashion image-1"></div><strong>Maison Afi</strong><span>Cotonou · Sur-mesure</span><small>4,9 · 32 avis</small><button class="secondary small">Voir la vitrine</button></article><article><div class="mini-fashion image-2"></div><strong>Atelier Néo</strong><span>Porto-Novo · Homme & femme</span><small>4,8 · 21 avis</small><button class="secondary small">Voir la vitrine</button></article><article><div class="mini-fashion image-3"></div><strong>Studio Awa</strong><span>Abomey-Calavi · Prêt-à-porter</span><small>4,7 · 18 avis</small><button class="secondary small">Voir la vitrine</button></article></div>`;
    default: return `<div class="client-home-grid"><div class="client-hero-card"><span class="eyebrow">MA PROCHAINE ÉTAPE</span><h3>Votre robe est en <em>essayage.</em></h3><p>Commande #1048 · Maison Afi</p><div class="big-progress"><i style="width:82%"></i></div><div><strong>82%</strong><span>Livraison estimée : 30 sept.</span></div><button class="primary small" id="seeOrder">Voir ma commande</button></div><div class="panel"><div class="panel-title"><h3>Raccourcis</h3></div><button class="quick-action" data-nav="measurements">◎ Mes profils de mesures <span>↗</span></button><button class="quick-action" data-nav="client-orders">◫ Toutes mes commandes <span>↗</span></button><button class="quick-action" data-nav="atelier-discovery">⌕ Découvrir un atelier <span>↗</span></button><button class="quick-action">▣ Mes reçus <span>↗</span></button></div></div>`;
  }
}

function renderAdmin(){
  switch(currentView){
    case 'admin-stylists': return `<div class="content-toolbar"><div><h3>Stylistes inscrits</h3><p>Comptes, profils publics et statut de publication.</p></div></div><div class="table-card"><div class="table-head"><span>Atelier</span><span>Ville</span><span>Commandes</span><span>Statut</span><span></span></div>${[['Maison Afi','Cotonou',42,'Publié'],['Atelier Néo','Porto-Novo',28,'Publié'],['Studio Awa','Abomey-Calavi',19,'À valider'],['Maison Kora','Cotonou',37,'Publié']].map(x=>`<div class="table-row"><div><strong>${x[0]}</strong><small>Compte professionnel</small></div><span>${x[1]}</span><span>${x[2]}</span><span><b class="status ${x[3]==='Publié'?'ready':'pending'}">${x[3]}</b></span><button class="ghost-btn">Ouvrir</button></div>`).join('')}</div>`;
    case 'admin-orders': return `<div class="app-stats">${renderStat('Commandes totales','1 284','ce mois')}${renderStat('En production','317','tous ateliers')}${renderStat('Montant encaissé','38,4 M','FCFA · cumul mois')}${renderStat('Paiements en attente','64','à surveiller')}</div><div class="panel"><div class="panel-title"><h3>Flux plateforme</h3><span class="muted">Données démo</span></div><div class="admin-bars">${[62,78,48,85,71,92,66].map((v,i)=>`<div><i style="height:${v}%"></i><span>${['L','M','M','J','V','S','D'][i]}</span></div>`).join('')}</div></div>`;
    case 'admin-moderation': return `<div class="moderation"><article><span class="flag">À vérifier</span><strong>Photo de collection</strong><p>Atelier Néo · publication du 27 sept.</p><button class="secondary small">Examiner</button></article><article><span class="flag">À vérifier</span><strong>Avis client signalé</strong><p>Maison Kora · commande #1021</p><button class="secondary small">Examiner</button></article></div>`;
    default: return `<div class="welcome-strip"><div><span class="eyebrow">SUPERVISION · BÉNIN</span><h3>La plateforme en un coup d’œil.</h3><p>Activité globale des ateliers et commandes.</p></div><div class="date-card"><strong>2026</strong><span>VERSION MVP</span></div></div><div class="app-stats">${renderStat('Stylistes actifs','86','comptes professionnels')}${renderStat('Clients','4 920','comptes ouverts')}${renderStat('Commandes','1 284','ce mois')}${renderStat('Volume','38,4 M','FCFA · mois')}</div><div class="two-col"><div class="panel"><div class="panel-title"><h3>Répartition des comptes</h3></div><div class="big-metric"><strong>86</strong><span>stylistes</span></div><div class="metric-line"><span>Publiés</span><b>72</b></div><div class="metric-line"><span>En attente</span><b>9</b></div><div class="metric-line"><span>Inactifs</span><b>5</b></div></div><div class="panel"><div class="panel-title"><h3>Activité récente</h3></div><div class="activity"><span>+12</span><p>Nouveaux clients aujourd’hui</p></div><div class="activity"><span>+34</span><p>Commandes créées cette semaine</p></div><div class="activity"><span>8</span><p>profils stylistes à modérer</p></div></div></div>`;
  }
}

function bindView(){
  $$('[data-nav]').forEach(b=>b.onclick=()=>{currentView=b.dataset.nav;renderApp()});
  $('#newOrder')?.addEventListener('click',()=>toast('Nouvelle commande : formulaire métier prêt à être relié à Laravel.'));
  $('#newClient')?.addEventListener('click',()=>toast('Formulaire client : prochaine étape, enregistrement MySQL.'));
  $('#advanceAll')?.addEventListener('click',()=>{data.orders.forEach(o=>{if(o.progress<100){o.progress=Math.min(100,o.progress+17);if(o.progress>=100)o.stage='Prêt'; else if(o.progress>=80)o.stage='Essayage'; else if(o.progress>=60)o.stage='Couture'; else if(o.progress>=40)o.stage='Coupe'; else if(o.progress>=20)o.stage='Tissu validé';}}); save(); renderApp(); toast('Production simulée.');});
  $('#addStock')?.addEventListener('click',()=>toast('Ajout matière : formulaire prêt pour la connexion API.'));
  $('#newAppt')?.addEventListener('click',()=>toast('Rendez-vous : le calendrier métier sera connecté au backend.'));
  $('#newProfile')?.addEventListener('click',()=>toast('Création de profil : prête à être persistée côté backend.'));
  $('#discoverBtn')?.addEventListener('click',()=>{currentView='atelier-discovery';renderApp()});
  $('#seeOrder')?.addEventListener('click',()=>{currentView='client-orders';renderApp()});
  $$('.ghost-btn[data-client]').forEach(b=>b.onclick=()=>toast(`Fiche de ${b.dataset.client} : historique des mesures à brancher au détail client.`));
}

$('#openAuth').onclick=openAuth; $('#finalAuth').onclick=openAuth; $('#enterBtn').onclick=()=>{document.documentElement.classList.add('entered'); setTimeout(openAuth,650)};
$$('[data-close-auth]').forEach(b=>b.onclick=closeAuth);
$$('[data-role]').forEach(b=>b.onclick=()=>enterRole(b.dataset.role));
$('#logoutBtn').onclick=exitApp;
$('#notifyBtn').onclick=()=>toast('Aucune notification non lue.');
$('#profileBtn').onclick=()=>toast('Profil utilisateur : connexion backend à venir.');

$$('a[href^="#"]').forEach(a=>a.addEventListener('click',e=>{const t=$(a.getAttribute('href')); if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth'})}}));

if(currentRole) showApp();

if('serviceWorker' in navigator){ navigator.serviceWorker.register('./sw.js').catch(()=>{}); }
