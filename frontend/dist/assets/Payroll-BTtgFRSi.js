import{u as V,q as g,o as A,b as r,c as i,d as t,t as o,f as u,F as K,r as Y,a as z,s as M,g as v,w as q,j as J,N as j,h as Q}from"./index-HpyaIqqw.js";const X={class:"min-h-screen bg-gray-50 font-light"},Z={class:"bg-white border-b border-gray-100 px-8 py-5"},G={class:"flex items-center justify-between"},tt={class:"flex items-center gap-2"},et={class:"text-sm text-gray-700 px-3 py-1.5 bg-white border border-gray-200 rounded-lg min-w-[190px] text-center"},st=["disabled"],at={class:"px-8 py-6 max-w-7xl mx-auto"},ot={class:"grid grid-cols-2 md:grid-cols-4 gap-4 mb-6"},lt={class:"bg-white border border-gray-100 rounded-xl p-4"},nt={class:"text-2xl font-normal text-gray-800"},rt={class:"bg-white border border-gray-100 rounded-xl p-4"},it={class:"text-2xl font-normal text-gray-800"},dt={class:"bg-white border border-gray-100 rounded-xl p-4"},pt={class:"text-2xl font-normal text-gray-800"},ct={class:"bg-white border border-gray-100 rounded-xl p-4"},xt={class:"text-2xl font-normal text-green-800"},gt={key:0,class:"flex items-center justify-center py-24"},ut={key:1,class:"bg-white border border-gray-100 rounded-xl overflow-hidden"},yt={class:"overflow-x-auto -mx-px"},ht={class:"w-full text-sm"},mt={class:"divide-y divide-gray-50"},ft={key:0},vt={class:"px-5 py-3.5"},bt={class:"flex items-center gap-2.5"},_t={class:"w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0"},kt={class:"text-green-800 text-xs font-normal"},Dt={class:"text-gray-800 font-normal text-sm"},wt={class:"text-xs text-gray-400"},St={class:"px-5 py-3.5"},$t={class:"px-5 py-3.5 text-gray-500 text-sm"},Ct={class:"px-5 py-3.5 text-right text-gray-700"},Pt={class:"px-5 py-3.5 text-right"},zt={class:"px-5 py-3.5 text-right"},Mt={key:0,class:"flex items-center justify-end gap-1.5"},jt=["onKeyup"],Tt=["onClick"],Wt=["onClick"],Bt={class:"px-5 py-3.5 text-right"},Et={key:0,class:"text-xs text-green-600 mt-0.5"},Ft={key:1,class:"text-xs text-orange-500 mt-0.5"},Lt={class:"px-5 py-3.5 text-center"},Rt=["onClick","disabled"],Ht={key:0},Nt={class:"border-t-2 border-gray-100 bg-gray-50/80"},It={class:"px-5 py-3 text-right text-sm font-normal text-gray-700"},Ot={class:"px-5 py-3 text-right text-sm font-normal text-green-800"},At={__name:"Payroll",setup(Ut){const T=V();function b(e){const s=new Date(e),a=s.getDay(),l=a===0?-6:1-a;return s.setDate(s.getDate()+l),s.toISOString().slice(0,10)}const _=new Date().toISOString().slice(0,10),d=g(b(_)),k=M(()=>d.value===b(_));function W(){const e=new Date(d.value);e.setDate(e.getDate()-7),d.value=e.toISOString().slice(0,10),y()}function B(){if(k.value)return;const e=new Date(d.value);e.setDate(e.getDate()+7),d.value=e.toISOString().slice(0,10),y()}function E(){d.value=b(_),y()}function F(e){const s=new Date(e),a=new Date(e);a.setDate(a.getDate()+6);const l={month:"short",day:"numeric"};return`${s.toLocaleDateString("en-US",l)} – ${a.toLocaleDateString("en-US",{...l,year:"numeric"})}`}const D=g(!1),p=g([]),c=M(()=>({staffCount:p.value.length,staffPresent:p.value.filter(e=>e.hours_worked>0).length,totalHours:p.value.reduce((e,s)=>e+s.hours_worked,0).toFixed(2),totalPay:p.value.reduce((e,s)=>e+s.total_pay,0)}));async function y(){D.value=!0;try{const e=await z.get("/admin/payroll/weekly",{params:{week_start:d.value}});p.value=e.data?.data?.staff??[]}catch{p.value=[]}finally{D.value=!1}}A(y);const h=g(null),x=g(0);function L(e){h.value=e.user_id,x.value=e.hourly_rate}function S(){h.value=null}async function $(e){try{await z.post("/admin/payroll/rate",{user_id:e.user_id,rate:x.value}),e.hourly_rate=x.value,e.total_pay=parseFloat((e.hours_worked*e.hourly_rate).toFixed(2)),h.value=null}catch(s){T.error(s?.response?.data?.message||"Failed to update rate")}}function R(e){const s=(()=>{const w=new Date(d.value);return w.setDate(w.getDate()+6),w.toISOString().slice(0,10)})(),a=e.total_pay,l=Math.round(e.hours_worked*e.hourly_rate*100)/100,C=H(a),m=e.late_deduction||0,P=Math.round((C+m)*100)/100,O=Math.max(0,Math.round((a-P)*100)/100),U=`<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Payslip – ${e.name}</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; font-size:13px; color:#333; padding:40px; background:#fff; }
    .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #1d4ed8; padding-bottom:20px; margin-bottom:28px; }
    .hotel-name { font-size:22px; font-weight:600; color:#1d4ed8; letter-spacing:-0.5px; }
    .hotel-sub  { font-size:11px; color:#888; margin-top:3px; }
    .slip-title { font-size:14px; font-weight:600; color:#555; text-align:right; }
    .slip-ref   { font-size:11px; color:#aaa; margin-top:4px; text-align:right; }
    .section    { margin-bottom:22px; }
    .section h3 { font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#aaa; margin-bottom:10px; }
    .grid2 { display:grid; grid-template-columns:1fr 1fr; gap:10px 40px; }
    .field label { font-size:10px; color:#aaa; display:block; margin-bottom:2px; }
    .field value, .field span { font-size:13px; color:#333; }
    .table { width:100%; border-collapse:collapse; margin-top:4px; }
    .table th { text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:0.8px; color:#aaa; border-bottom:1px solid #eee; padding:6px 0; }
    .table td { padding:8px 0; border-bottom:1px solid #f5f5f5; font-size:13px; }
    .table td:last-child, .table th:last-child { text-align:right; }
    .total-row td { font-weight:700; font-size:15px; border-top:2px solid #1d4ed8; color:#1d4ed8; border-bottom:none; padding-top:14px; }
    .gross-row td { font-weight:600; font-size:13px; border-top:1px solid #e5e7eb; color:#374151; border-bottom:2px solid #e5e7eb; padding-top:8px; padding-bottom:8px; }
    .deduct-row td { color:#92400e; }
    .deduct-total-row td { font-weight:600; font-size:13px; border-top:1px solid #fde68a; color:#b45309; border-bottom:none; padding-top:8px; }
    .footer { margin-top:40px; border-top:1px solid #eee; padding-top:16px; display:flex; justify-content:space-between; }
    .sig-line { width:160px; border-top:1px solid #ccc; padding-top:6px; font-size:10px; color:#aaa; text-align:center; }
    @media print {
      body { padding:20px; }
      @page { margin:15mm; }
    }
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="hotel-name">Joanna's Nook Bed & Breakfast</div>
      <div class="hotel-sub">Payroll Department</div>
    </div>
    <div>
      <div class="slip-title">PAYSLIP</div>
      <div class="slip-ref">Week of ${d.value}</div>
    </div>
  </div>

  <div class="section">
    <h3>Employee Details</h3>
    <div class="grid2">
      <div class="field"><label>Full Name</label><span>${e.name}</span></div>
      <div class="field"><label>Email</label><span>${e.email}</span></div>
      <div class="field"><label>Role</label><span style="text-transform:capitalize">${e.role}</span></div>
      <div class="field"><label>Department</label><span>${e.department||"—"}</span></div>
    </div>
  </div>

  <div class="section">
    <h3>Pay Period</h3>
    <div class="grid2">
      <div class="field"><label>Week Start</label><span>${d.value}</span></div>
      <div class="field"><label>Week End</label><span>${s}</span></div>
    </div>
  </div>

  <div class="section">
    <h3>Earnings</h3>
    <table class="table">
      <thead>
        <tr>
          <th>Description</th>
          <th style="text-align:right">Days</th>
          <th style="text-align:right">Hours</th>
          <th style="text-align:right">Rate / hr</th>
          <th style="text-align:right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Weekly Wages</td>
          <td style="text-align:right">${e.days_present}d</td>
          <td style="text-align:right">${e.hours_worked}h</td>
          <td style="text-align:right">₱${n(e.hourly_rate)}</td>
          <td style="text-align:right">₱${n(l)}</td>
        </tr>
        ${e.paid_leave_days>0?`
        <tr>
          <td>Paid Leave <span style="font-size:11px;color:#6b7280">(${e.paid_leave_days} day${e.paid_leave_days!==1?"s":""} × 8h @ ₱${n(e.hourly_rate)}/hr)</span></td>
          <td colspan="3"></td>
          <td style="text-align:right">₱${n(e.leave_pay)}</td>
        </tr>`:""}
        ${e.unpaid_leave_days>0?`
        <tr style="color:#92400e">
          <td>Unpaid Leave <span style="font-size:11px">(${e.unpaid_leave_days} day${e.unpaid_leave_days!==1?"s":""} — no pay)</span></td>
          <td colspan="3"></td>
          <td style="text-align:right">—</td>
        </tr>`:""}
        <tr class="gross-row">
          <td colspan="4">Gross Pay</td>
          <td style="text-align:right">₱${n(a)}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="section">
    <h3>Deductions</h3>
    <table class="table">
      <thead>
        <tr>
          <th>Description</th>
          <th style="text-align:right">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr class="deduct-row">
          <td>SSS Contribution (Employee Share, 4.5%)</td>
          <td style="text-align:right">–&#8369;${n(C)}</td>
        </tr>
        <tr class="deduct-row">
          <td>Late Deduction${m>0?" ("+e.total_late_minutes+" min late)":""}</td>
          <td style="text-align:right">${m>0?"–&#8369;"+n(m):"—"}</td>
        </tr>
        <tr class="deduct-total-row">
          <td>Total Deductions</td>
          <td style="text-align:right">–&#8369;${n(P)}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="section">
    <table class="table">
      <tbody>
        <tr class="total-row">
          <td>NET PAY</td>
          <td style="text-align:right">&#8369;${n(O)}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <div class="sig-line">Prepared By</div>
    <div class="sig-line">Received By</div>
  </div>
</body>
</html>`,f=window.open("","_blank","width=700,height=900");f.document.write(U),f.document.close(),f.focus(),setTimeout(()=>f.print(),400)}function n(e){return Number(e||0).toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2})}function H(e){if(!e||e<=0)return 0;const s=e*52/12,a=Math.min(Math.max(Math.ceil(s/500)*500,4e3),3e4),l=Math.round(a*.045/22.5)*22.5;return Math.round(l*12/52*100)/100}function N(e){return e?.split(" ").map(s=>s[0]).slice(0,2).join("").toUpperCase()??"?"}function I(e){return{admin:"bg-purple-50 text-purple-700",manager:"bg-green-50 text-green-800",front_desk:"bg-cyan-50 text-cyan-700",housekeeping:"bg-green-50 text-green-700",chef:"bg-orange-50 text-orange-700",accountant:"bg-yellow-50 text-yellow-700"}[e]??"bg-gray-100 text-gray-600"}return(e,s)=>(r(),i("div",X,[t("div",Z,[t("div",G,[s[3]||(s[3]=t("div",null,[t("h1",{class:"text-xl font-normal text-gray-800"},"Payroll"),t("p",{class:"text-sm text-gray-400 mt-0.5"},"Weekly hours worked and pay summary")],-1)),t("div",tt,[t("button",{onClick:W,class:"w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 transition"},[...s[1]||(s[1]=[t("svg",{class:"w-4 h-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"1.5",d:"M15 19l-7-7 7-7"})],-1)])]),t("div",et,o(F(d.value)),1),t("button",{onClick:B,disabled:k.value,class:"w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 transition disabled:opacity-30 disabled:cursor-not-allowed"},[...s[2]||(s[2]=[t("svg",{class:"w-4 h-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"1.5",d:"M9 5l7 7-7 7"})],-1)])],8,st),k.value?u("",!0):(r(),i("button",{key:0,onClick:E,class:"text-xs text-green-800 hover:underline px-2"},"Today"))])])]),t("div",at,[t("div",ot,[t("div",lt,[t("p",nt,o(c.value.staffCount),1),s[4]||(s[4]=t("p",{class:"text-xs text-gray-400 mt-0.5"},"Staff Members",-1))]),t("div",rt,[t("p",it,o(c.value.totalHours)+"h",1),s[5]||(s[5]=t("p",{class:"text-xs text-gray-400 mt-0.5"},"Total Hours Worked",-1))]),t("div",dt,[t("p",pt,o(c.value.staffPresent),1),s[6]||(s[6]=t("p",{class:"text-xs text-gray-400 mt-0.5"},"Staff with Hours",-1))]),t("div",ct,[t("p",xt,"₱"+o(n(c.value.totalPay)),1),s[7]||(s[7]=t("p",{class:"text-xs text-gray-400 mt-0.5"},"Total Weekly Pay",-1))])]),D.value?(r(),i("div",gt,[...s[8]||(s[8]=[t("div",{class:"w-8 h-8 border-2 border-blue-700 border-t-transparent rounded-full animate-spin"},null,-1)])])):(r(),i("div",ut,[t("div",yt,[t("table",ht,[s[16]||(s[16]=t("thead",null,[t("tr",{class:"border-b border-gray-100 bg-gray-50"},[t("th",{class:"text-left text-xs text-gray-400 font-normal px-5 py-3"},"Employee"),t("th",{class:"text-left text-xs text-gray-400 font-normal px-5 py-3"},"Role"),t("th",{class:"text-left text-xs text-gray-400 font-normal px-5 py-3"},"Department"),t("th",{class:"text-right text-xs text-gray-400 font-normal px-5 py-3"},"Days"),t("th",{class:"text-right text-xs text-gray-400 font-normal px-5 py-3"},"Hours Worked"),t("th",{class:"text-right text-xs text-gray-400 font-normal px-5 py-3"},"Rate / hr"),t("th",{class:"text-right text-xs text-gray-400 font-normal px-5 py-3"},"Total Pay"),t("th",{class:"text-center text-xs text-gray-400 font-normal px-5 py-3"},"Payslip")])],-1)),t("tbody",mt,[p.value.length?u("",!0):(r(),i("tr",ft,[...s[9]||(s[9]=[t("td",{colspan:"8",class:"text-center py-16 text-gray-400 text-sm"},"No staff data found.",-1)])])),(r(!0),i(K,null,Y(p.value,a=>(r(),i("tr",{key:a.user_id,class:"hover:bg-gray-50/60 transition"},[t("td",vt,[t("div",bt,[t("div",_t,[t("span",kt,o(N(a.name)),1)]),t("div",null,[t("p",Dt,o(a.name),1),t("p",wt,o(a.email),1)])])]),t("td",St,[t("span",{class:v([I(a.role),"text-xs px-2 py-0.5 rounded-full capitalize"])},o(a.role),3)]),t("td",$t,o(a.department||"—"),1),t("td",Ct,o(a.days_present),1),t("td",Pt,[t("span",{class:v(a.hours_worked>0?"text-gray-800 font-normal":"text-gray-300")},o(a.hours_worked)+"h ",3)]),t("td",zt,[h.value===a.user_id?(r(),i("div",Mt,[s[10]||(s[10]=t("span",{class:"text-gray-400 text-xs"},"₱",-1)),q(t("input",{"onUpdate:modelValue":s[0]||(s[0]=l=>x.value=l),type:"number",min:"0",step:"0.01",class:"w-20 border border-green-300 rounded px-2 py-1 text-sm text-right focus:outline-none focus:ring-1 focus:ring-blue-400",onKeyup:[j(l=>$(a),["enter"]),j(S,["esc"])],autofocus:""},null,40,jt),[[J,x.value,void 0,{number:!0}]]),t("button",{onClick:l=>$(a),class:"text-green-600 hover:text-green-700 text-xs font-normal"},"✓",8,Tt),t("button",{onClick:S,class:"text-gray-400 hover:text-gray-600 text-xs"},"✕")])):(r(),i("div",{key:1,class:"flex items-center justify-end gap-1 group cursor-pointer",onClick:l=>L(a)},[t("span",{class:v(a.hourly_rate>0?"text-gray-700":"text-gray-300")}," ₱"+o(n(a.hourly_rate)),3),s[11]||(s[11]=t("svg",{class:"w-3 h-3 text-gray-300 group-hover:text-amber-600 transition shrink-0",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-1.414.93l-3 1a1 1 0 01-1.273-1.273l1-3a4 4 0 01.93-1.414z"})],-1))],8,Wt))]),t("td",Bt,[t("span",{class:v(a.total_pay>0?"text-green-800 font-normal":"text-gray-300")}," ₱"+o(n(a.total_pay)),3),a.paid_leave_days>0?(r(),i("div",Et," incl. "+o(a.paid_leave_days)+"d paid leave ",1)):u("",!0),a.unpaid_leave_days>0?(r(),i("div",Ft,o(a.unpaid_leave_days)+"d unpaid leave ",1)):u("",!0)]),t("td",Lt,[t("button",{onClick:l=>R(a),disabled:a.total_pay<=0,class:"text-xs text-green-800 border border-green-200 bg-green-50 px-3 py-1.5 rounded-lg hover:bg-green-100 transition disabled:opacity-30 disabled:cursor-not-allowed"},[...s[12]||(s[12]=[t("svg",{class:"w-3.5 h-3.5 inline -mt-0.5 mr-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"1.5",d:"M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"})],-1),Q(" Download ",-1)])],8,Rt)])]))),128))]),p.value.length?(r(),i("tfoot",Ht,[t("tr",Nt,[s[13]||(s[13]=t("td",{colspan:"4",class:"px-5 py-3 text-xs text-gray-400"},"Total",-1)),t("td",It,o(c.value.totalHours)+"h",1),s[14]||(s[14]=t("td",{class:"px-5 py-3"},null,-1)),t("td",Ot,"₱"+o(n(c.value.totalPay)),1),s[15]||(s[15]=t("td",{class:"px-5 py-3"},null,-1))])])):u("",!0)])])])),s[17]||(s[17]=t("p",{class:"text-xs text-gray-400 mt-3"}," Click the pencil icon next to a rate to edit it. Changes are saved immediately and applied to future payslips. ",-1))])]))}};export{At as default};
