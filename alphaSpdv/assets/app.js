// Filtro de tabelas
document.querySelectorAll('[data-filtro]').forEach(inp => {
  inp.addEventListener('input', () => {
    const t = inp.value.toLowerCase();
    document.querySelectorAll('#' + inp.dataset.filtro + ' tr').forEach((tr, i) => {
      if (i > 0) tr.style.display = tr.textContent.toLowerCase().includes(t) ? '' : 'none';
    });
  });
});
// Máscara de telefone
const tel = document.querySelector('input[name=telefone]');
if (tel) tel.addEventListener('input', () => {
  let d = tel.value.replace(/\D/g, '').slice(0, 11);
  tel.value = d.length > 10 ? d.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3')
            : d.length > 6 ? d.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3')
            : d.length > 2 ? d.replace(/(\d{2})(\d*)/, '($1) $2') : d;
});
// Total da OS em tempo real
const total = document.getElementById('total');
if (total) {
  const calc = () => {
    let s = parseFloat(document.getElementById('mao').value) || 0;
    document.querySelectorAll('.pv').forEach(pv => {
      const q = pv.closest('tr').querySelector('.qtd');
      s += (parseInt(q.value) || 0) * (parseFloat(pv.value) || 0);
      pv.classList.toggle('alt', parseFloat(pv.value) !== parseFloat(pv.dataset.base));
    });
    total.textContent = 'R$ ' + s.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
  };
  document.querySelectorAll('.qtd, .pv, #mao').forEach(i => i.addEventListener('input', calc));
  calc();
}
// Mensagem some sozinha
const toast = document.querySelector('.toast');
if (toast) setTimeout(() => toast.classList.add('sumir'), 3500);
