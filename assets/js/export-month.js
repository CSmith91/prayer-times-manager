function ptmDownloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');
    doc.autoTable({ html: '#ptm-table' });
    doc.save("Prayer-Times-" + ptm_vars.selectedMonth + '-' + ptm_vars.selectedYear + ".pdf");
}