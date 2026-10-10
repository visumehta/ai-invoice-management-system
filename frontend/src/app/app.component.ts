import { Component, inject, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { InvoiceService } from './services/invoice.service';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { debounceTime } from 'rxjs/internal/operators/debounceTime';
import { distinctUntilChanged } from 'rxjs/internal/operators/distinctUntilChanged';
import { DecimalPipe } from '@angular/common';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, ReactiveFormsModule, DecimalPipe],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent implements OnInit {
  title = 'frontend';

  _invoiceService = inject(InvoiceService);

  invoices: any[] = [];
  allInvoices: any[] = [];

  selectedInvoice: any = null;
  selectedFile: File | null = null;
  selectedFiles: File[] = [];
  isUploading = false;

  uploadProgress = 0;
  currentFileName = '';

  successfulFiles: string[] = [];
  failedFiles: File[] = [];

  searchControl = new FormControl('');
  marketplaceControl = new FormControl('');
  monthControl = new FormControl('');
  marketplaces: string[] = [];

  totalInvoices = 0;
  totalSales = 0;
  totalTax = 0;
  totalQuantity = 0;

  // Marketplace dashboard
  amazonInvoices = 0;
  amazonSales = 0;

  flipkartInvoices = 0;
  flipkartSales = 0;

  meeshoInvoices = 0;
  meeshoSales = 0;

  ngOnInit() {
    this.loadInvoices();

    this._invoiceService.getMarketplaces().subscribe({
      next: (response: any) => {
        if (response.success) {
          this.marketplaces = response.data;
        }
      },
      error: (error) => {
        console.error('Failed to load marketplaces:', error);
      }
    });

    this.searchControl.valueChanges.pipe(
      debounceTime(300),
      distinctUntilChanged()
    ).subscribe(searchTerm => {
      this.searchInvoices(searchTerm ?? '');
    });

    this.marketplaceControl.valueChanges.subscribe(marketplace => {
      this.filterInvoices(this.searchControl.value ?? '', marketplace ?? '', this.monthControl.value ?? '');
    });

    this.monthControl.valueChanges.subscribe(month => {
      this.filterInvoices(this.searchControl.value ?? '', this.marketplaceControl.value ?? '', month ?? '');
    })
  }

  calculateDashboard(): void {
    this.totalInvoices = this.allInvoices.length;

    this.totalSales = this.allInvoices.reduce((sum, invoice) => sum + Number(invoice.total_amount || 0), 0);
    this.totalTax = this.allInvoices.reduce((sum, invoice) => sum + Number(invoice.gst_amount || 0), 0);
    this.totalQuantity = this.allInvoices.reduce((sum, invoice) => sum + Number(invoice.quantity || 0), 0);

    this.amazonInvoices = 0;
    this.amazonSales = 0;

    this.flipkartInvoices = 0;
    this.flipkartSales = 0;

    this.meeshoInvoices = 0;
    this.meeshoSales = 0;

    this.allInvoices.forEach(invoice => {
      const marketplace = invoice.marketplace?.toLowerCase();
      const sales = Number(invoice.total_amount || 0);
      console.log(invoice)

      if (marketplace.includes('amazon')) {
        this.amazonInvoices++;
        this.amazonSales += sales;
      }

      if (marketplace.includes('flipkart')) {
        this.flipkartInvoices++;
        this.flipkartSales += sales;
      }

      if (marketplace.includes('meesho')) {
        this.meeshoInvoices++;
        this.meeshoSales += sales;
      }
    })
  }

  filterInvoices(searchTerm: string, marketplace: string, month: string): void {
    const term = searchTerm.trim().toLowerCase();

    this.invoices = this.allInvoices.filter(invoice => {
      const customerName = invoice.customer_name?.toLowerCase() ?? '';
      const orderId = String(invoice.order_id ?? '').toLowerCase();
      const invoiceNo = String(invoice.invoice_no ?? '').toLowerCase();
      const invoiceDate = String(invoice.invoice_date ?? '');
      const orderDate = String(invoice.order_date ?? '');
      const sku = String(invoice.sku ?? '').toLowerCase();
      const productName = String(invoice.product_name ?? '').toLowerCase();
      const amount = String(invoice.total_amount ?? '');
      const sellerName = String(invoice.seller_name ?? '').toLowerCase();

      const matchesSearch =
        !term ||
        customerName.includes(term) ||
        orderId.toLowerCase().includes(term) ||
        invoiceNo.includes(term) ||
        invoiceDate.toLowerCase().includes(term) ||
        orderDate.toLowerCase().includes(term) ||
        sku.includes(term) ||
        productName.includes(term) ||
        amount.includes(term) ||
        sellerName.includes(term);

      const matchesMarketplace =
        !marketplace ||
        String(invoice.normalized_marketplace  ?? '').toLowerCase() === marketplace.toLowerCase();

      const selectedDate = invoiceDate || orderDate;
      const matchesMonth =
        !month || selectedDate.startsWith(month);

      return matchesSearch && matchesMarketplace && matchesMonth;
    })
  }

  searchInvoices(searchTerm: string): void {
    this.filterInvoices(searchTerm, this.marketplaceControl.value ?? '', this.monthControl.value ?? '');
  }

  viewInvoice(invoice: any) {
    this.selectedInvoice = invoice;
    console.log('Selected Invoice:', this.selectedInvoice);
  }

  onSelectedFiles(event: Event): void {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
      // this.selectedFile = target.files[0];
      this.selectedFiles = Array.from(target.files);
      console.log('Selected Files:', this.selectedFiles);
    }
  }

  uploadBulkInvoices(): void {
    if (this.selectedFiles.length === 0) {
      return;
    }
    console.log('Uploading files:', this.selectedFiles);
    this.isUploading = true;
    this.uploadProgress = 0;

    this.successfulFiles = [];
    this.failedFiles = [];

    this.uploadNextFile(0);
  }

  uploadNextFile(index: number): void {

    // All files completed
    if (index >= this.selectedFiles.length) {


      this.isUploading = false;

      this.loadInvoices();
      this.selectedFiles = [];

      console.log('All invoices processed successfully');

      return;
    }

    const file = this.selectedFiles[index];
    this.currentFileName = file.name;
    this.uploadProgress = Math.round(
      (index / this.selectedFiles.length) * 100
    );

    console.log(
      `Uploading ${index + 1}/${this.selectedFiles.length}:`,
      file.name
    );

    this._invoiceService.uploadInvoice(file).subscribe({

      next: (response: any) => {

        console.log(
          'Upload response:',
          file.name,
          response
        );

        if (response.success) {

          this.successfulFiles.push(file.name);

          this.uploadProgress = Math.round(
            ((index + 1) / this.selectedFiles.length) * 100
          );

          this.loadInvoices();  // Refresh the list of invoices after a successful upload

          // Upload next file
          this.uploadNextFile(index + 1);

        } else {

          this.failedFiles.push(file);
          // Stop on API error
          console.error(
            'Upload failed:',
            file.name,
            response.message
          );

          this.isUploading = false;
        }
      },

      error: (error) => {

        this.failedFiles.push(file);

        console.error(
          'Upload failed:',
          file.name,
          error
        );

        // Stop loading immediately
        this.isUploading = false;
      }

    });
  }

  retryFailedInvoices(): void {
    if (this.failedFiles.length === 0) {
      return;
    }

    this.selectedFiles = [...this.failedFiles];

    this.failedFiles = [];
    this.successfulFiles = [];

    this.uploadProgress = 0;
    this.isUploading = true;

    this.uploadNextFile(0);
  }


  uploadInvoice(): void {
    if (!this.selectedFile) {
      return;
    }
    this.isUploading = true;

    this._invoiceService.uploadInvoice(this.selectedFile).subscribe({
      next: (response: any) => {
        console.log('Upload successful:', response);
        if (response.success) {
          this.loadInvoices();  // Refresh the list of invoices after a successful upload
          this.selectedFile = null;  // Clear the selected file after upload
        } else {
          console.error('Upload failed:', response.message);
        }
        this.isUploading = false;
      },
      error: (error) => {
        console.error('Upload failed:', error);
        this.isUploading = false;
      }
    })
  }

  loadInvoices(): void {
    this._invoiceService.getInvoices().subscribe({
      next: (response: any) => {
        if (response.success) {
          // this.invoices = response.data;
          this.allInvoices = response.data;  // Store all invoices for filtering
          this.calculateDashboard();

          this.filterInvoices(
            this.searchControl.value ?? '',
            this.marketplaceControl.value ?? '',
            this.monthControl.value ?? ''
          )
        }
      },
      error: (error) => {
        console.error('Failed to load invoices:', error);
      }
    })
  }
}
