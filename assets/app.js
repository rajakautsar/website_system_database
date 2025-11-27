// assets/app.js
const noProjectInput = document.getElementById("noProject");
const namaProjectInput = document.getElementById("namaProject");
const clientInput = document.getElementById("client");
const venueInput = document.getElementById("venue");
const eventDateStartInput = document.getElementById("eventDateStart");
const eventDateEndInput = document.getElementById("eventDateEnd");
const contractTypeSelect = document.getElementById("contractType");
const remarksInput = document.getElementById("remarks");
const rabSubmitInput = document.getElementById("rabSubmit");
const rabInternalInput = document.getElementById("rabInternal");
const profitMarginInput = document.getElementById("profitMargin");
const profitPercentageInput = document.getElementById("profitPercentage");

const fileSPPHInput = document.getElementById("uploadSPPH");
const fileSPKInput = document.getElementById("uploadSPK");
const spkLinkInput = document.getElementById("spkLink");

const modal = document.getElementById("previewModal");
const previewBtn = document.getElementById("previewBtn");
const editBtn = document.getElementById("editBtn");
const closeModalBtn = document.getElementById("closeModal");
const confirmSubmitBtn = document.getElementById("confirmSubmitBtn");

function calculateProfitMargin() {
  const submit = parseFloat(rabSubmitInput.value) || 0;
  const internal = parseFloat(rabInternalInput.value) || 0;
  const profit = submit - internal;
  profitMarginInput.value = profit;
  profitPercentageInput.value = internal > 0 ? ((profit / internal) * 100).toFixed(2) : '0.00';
}
rabSubmitInput.addEventListener('input', calculateProfitMargin);
rabInternalInput.addEventListener('input', calculateProfitMargin);

previewBtn.addEventListener('click', () => {
  const html = `
    <p><strong>Category:</strong> ${document.getElementById('category').value}</p>
    <p><strong>No Project:</strong> ${noProjectInput.value}</p>
    <p><strong>Nama Project:</strong> ${namaProjectInput.value}</p>
    <p><strong>Client:</strong> ${clientInput.value}</p>
    <p><strong>Venue:</strong> ${venueInput.value}</p>
    <p><strong>Event Date:</strong> ${eventDateStartInput.value} s/d ${eventDateEndInput.value}</p>
    <p><strong>Contract Type:</strong> ${contractTypeSelect.value}</p>
    <p><strong>Remarks:</strong> ${remarksInput.value || '-'}</p>
    <p><strong>RAB Submit:</strong> Rp ${rabSubmitInput.value}</p>
    <p><strong>RAB Internal:</strong> Rp ${rabInternalInput.value}</p>
    <p><strong>Profit (Rp):</strong> Rp ${profitMarginInput.value}</p>
    <p><strong>Profit (%):</strong> ${profitPercentageInput.value}%</p>
    <p><strong>SPPH:</strong> ${fileSPPHInput.files[0]?.name || '(belum dipilih)'}</p>
    <p><strong>SPK file:</strong> ${fileSPKInput.files[0]?.name || '(tidak ada)'}</p>
    <p><strong>SPK link:</strong> ${spkLinkInput.value || '(tidak ada)'}</p>
  `;
  document.getElementById('previewContent').innerHTML = html;
  modal.style.display = 'block';
});

closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
editBtn.addEventListener('click', () => modal.style.display = 'none');

confirmSubmitBtn.addEventListener('click', () => {
  modal.style.display = 'none';
  submitForm();
});

async function submitForm() {
  // basic validation
  if (!noProjectInput.value || !namaProjectInput.value || !clientInput.value || !venueInput.value || !eventDateStartInput.value || !eventDateEndInput.value || !contractTypeSelect.value) {
    alert('Harap isi semua field wajib.');
    return;
  }
  if (fileSPPHInput.files.length === 0) {
    alert('Upload SPPH wajib.');
    return;
  }

  const fd = new FormData();
  fd.append('category', document.getElementById('category').value);
  fd.append('no_project', noProjectInput.value);
  fd.append('nama_project', namaProjectInput.value);
  fd.append('client', clientInput.value);
  fd.append('venue', venueInput.value);
  fd.append('event_date_start', eventDateStartInput.value);
  fd.append('event_date_end', eventDateEndInput.value);
  fd.append('contract_type', contractTypeSelect.value);
  fd.append('remarks', remarksInput.value);
  fd.append('rab_submit', rabSubmitInput.value);
  fd.append('rab_internal', rabInternalInput.value);
  fd.append('profit_margin', profitMarginInput.value);
  fd.append('profit_percentage', profitPercentageInput.value);
  fd.append('file_spph', fileSPPHInput.files[0]);
  if (fileSPKInput.files.length > 0) fd.append('file_spk', fileSPKInput.files[0]);
  fd.append('spk_link', spkLinkInput.value);

  try {
    const res = await fetch('./api/submit_form.php', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
      alert(json.message);
      localStorage.removeItem('REJECTED_FORM');
      document.getElementById('rabForm').reset();
      setTimeout(() => window.location = 'login.html', 1000);
    } else {
      alert('Gagal: ' + (json.message || 'server error'));
    }
  } catch (err) {
    console.error(err);
    alert('Error koneksi: ' + err.message);
  }
}
