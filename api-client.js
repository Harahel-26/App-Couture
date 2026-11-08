/* API adapter: set window.ATELIER_API_URL to your Laravel URL to enable backend mode. */
window.AtelierAPI = (() => {
  const base = (window.ATELIER_API_URL || '').replace(/\/$/, '');
  const tokenKey = 'atelierApiToken';
  async function request(path, options={}) {
    if (!base) throw new Error('API Laravel non configurée');
    const headers = {'Content-Type':'application/json','Accept':'application/json',...(options.headers||{})};
    const token = localStorage.getItem(tokenKey); if (token) headers.Authorization=`Bearer ${token}`;
    const res = await fetch(base+path,{...options,headers});
    if(!res.ok){let body={};try{body=await res.json()}catch{};throw new Error(body.message||`Erreur API ${res.status}`)}
    return res.status===204?null:res.json();
  }
  return {
    enabled: !!base,
    async login(credentials){const r=await request('/login',{method:'POST',body:JSON.stringify(credentials)});localStorage.setItem(tokenKey,r.token);return r;},
    async logout(){try{return await request('/logout',{method:'POST'});}finally{localStorage.removeItem(tokenKey);}},
    me(){return request('/me');},
    orders:{list(){return request('/stylist/orders');},create(data){return request('/stylist/orders',{method:'POST',body:JSON.stringify(data)});},update(id,data){return request(`/stylist/orders/${id}`,{method:'PATCH',body:JSON.stringify(data)});}},
    measurements:{list(){return request('/stylist/measurement-profiles');},create(data){return request('/stylist/measurement-profiles',{method:'POST',body:JSON.stringify(data)});}},
    stock:{list(){return request('/stylist/stock-items');},create(data){return request('/stylist/stock-items',{method:'POST',body:JSON.stringify(data)});},update(id,data){return request(`/stylist/stock-items/${id}`,{method:'PATCH',body:JSON.stringify(data)});}},
    appointments:{list(){return request('/stylist/appointments');},create(data){return request('/stylist/appointments',{method:'POST',body:JSON.stringify(data)});}},
    stylists(){return request('/stylists');}
  };
})();
