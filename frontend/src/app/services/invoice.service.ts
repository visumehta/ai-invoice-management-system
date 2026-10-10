import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class InvoiceService {
    private apiUrl =
    'http://localhost/AI-invoice-system/backend/api/invoices.php';
    private pdfTestUrl = 'http://localhost/AI-invoice-system/backend/api/pdf-test.php';
    private marketplaceUrl = 'http://localhost/AI-invoice-system/backend/api/marketplaces.php';

  constructor(private http: HttpClient) { }
  getInvoices() {
    return this.http.get(this.apiUrl);
  }

  uploadInvoice(file: File) {
    const formData = new FormData();
    formData.append('invoice_pdf', file);
    return this.http.post(this.pdfTestUrl, formData);
  }

getMarketplaces() {
  return this.http.get(this.marketplaceUrl);
}

}
