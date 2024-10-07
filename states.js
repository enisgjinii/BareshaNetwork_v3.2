document.addEventListener('DOMContentLoaded', async () => {
  try {
    // Kontrollimi i disponueshmërisë së localStorage
    if (typeof localStorage === 'undefined') {
      console.error("localStorage nuk është e disponueshme në këtë shfletues.");
      return;
    }

    // Menaxhimi i Tabave
    const tabs = document.querySelectorAll('.nav-link[data-bs-toggle="pill"]');
    if (!tabs || tabs.length === 0) {
      console.error("Asnjë tab nuk u gjet me selektorin '.nav-link[data-bs-toggle=\"pill\"]'.");
    }

    const tabContents = document.querySelectorAll(".tab-pane");
    if (!tabContents || tabContents.length === 0) {
      console.error("Asnjë përmbajtje tabu nuk u gjet me selektorin '.tab-pane'.");
    }

    const activeTab = localStorage.getItem("activeTab");
    if (activeTab && typeof activeTab !== 'string') {
      console.error("Vlera e 'activeTab' nga localStorage nuk është një string.");
    }

    if (activeTab) {
      let tabFound = false;
      tabs.forEach(tab => {
        if (!tab.id) {
          console.warn("Një tab pa ID është gjetur.");
          return;
        }
        const isActive = tab.id === activeTab;
        tab.classList.toggle("active", isActive);
        tab.setAttribute("aria-selected", isActive.toString());
        if (isActive) tabFound = true;
      });

      if (!tabFound) {
        console.warn(`Asnjë tab me ID '${activeTab}' nuk u gjet. Asnjë tab nuk do të aktivizohet.`);
      }

      let contentFound = false;
      tabContents.forEach(content => {
        if (!content.id) {
          console.warn("Një përmbajtje tabu pa ID është gjetur.");
          return;
        }
        const isActive = content.id === activeTab.replace("-tab", "");
        content.classList.toggle("show", isActive);
        content.classList.toggle("active", isActive);
        if (isActive) contentFound = true;
      });

      if (!contentFound) {
        console.warn(`Asnjë përmbajtje tabu me ID '${activeTab.replace("-tab", "")}' nuk u gjet.`);
      }
    }

    // Funksion Asinkron për Menaxhimin e Klikimeve të Tabave
    const handleTabClick = async (tab) => {
      try {
        if (!tab.id) {
          console.warn("Një tab pa ID është gjetur dhe nuk mund të ruhet në localStorage.");
          return;
        }
        localStorage.setItem("activeTab", tab.id);
      } catch (e) {
        console.error("Gabim gjatë ruajtjes së 'activeTab' në localStorage:", e);
      }
    };

    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        handleTabClick(tab);
      });
    });

    // Menaxhimi i Modalit
    const initializeModal = async () => {
      try {
        const myModalEl = document.getElementById("newInvoice");
        if (!myModalEl) {
          throw new Error('Elementi modal me ID "newInvoice" nuk u gjet.');
        }

        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
          throw new Error("Bootstrap nuk është i ngarkuar ose nuk ka klasën Modal.");
        }

        const myModal = new bootstrap.Modal(myModalEl);

        myModalEl.addEventListener("show.bs.modal", async (event) => {
          try {
            if (!event) {
              throw new Error("Ngjarja e modalit nuk është e disponueshme.");
            }

            const button = event.relatedTarget;
            if (!button) {
              console.warn("Butoni që e shkakton modalin nuk u gjet.");
            }

            const channelId = button?.dataset?.channelId;
            if (!channelId) {
              throw new Error("ID e kanalit nuk është dhënë.");
            }

            // Në rast se duhet të bëni një thirrje asinkrone për vlerën e YouTube Analytics
            const youtubeAnalyticsValue = await fetchYouTubeAnalytics(channelId); // Funksion i imagjinuar asinkron

            if (typeof youtubeAnalyticsValue !== 'string') {
              console.warn("Vlera e YouTube Analytics nuk është një string.");
            }

            const channelDisplay = document.getElementById("channel_display");
            if (channelDisplay) {
              channelDisplay.textContent = channelId;
            } else {
              console.warn('Elementi #channel_display nuk u gjet.');
            }

            const customerSelect = document.getElementById("customer_id");
            if (customerSelect) {
              if (customerSelect.options.length < 3) {
                console.warn('Select elementi #customer_id ka më pak se tre opsione.');
              }
              customerSelect.selectedIndex = 2; // Zgjidh opsionin e tretë (indeks 0-based)
              let optionFound = false;
              [...customerSelect.options].forEach(option => {
                if (option.dataset.youtube === channelId) {
                  option.selected = true;
                  optionFound = true;
                }
              });
              if (!optionFound) {
                console.warn(`Asnjë opsion me dataset.youtube '${channelId}' nuk u gjet në #customer_id.`);
              }
            } else {
              console.warn('Select elementi me ID "customer_id" nuk u gjet.');
            }

            const totalAmount = document.getElementById("total_amount");
            if (totalAmount) {
              totalAmount.value = youtubeAnalyticsValue;
            } else {
              console.warn('Elementi #total_amount nuk u gjet.');
            }

            // Kontrollime të tjera gabimesh
            const invoiceForm = document.getElementById("invoiceForm");
            if (!invoiceForm) {
              console.warn('Formulari me ID "invoiceForm" nuk u gjet.');
            }

            const submitButton = myModalEl.querySelector("button[type='submit']");
            if (!submitButton) {
              console.warn('Butoni i dërgimit në modal me ID "newInvoice" nuk u gjet.');
            }

            const invoiceDate = document.getElementById("invoice_date");
            if (!invoiceDate) {
              console.warn('Fusha e datës së faturës me ID "invoice_date" nuk u gjet.');
            }

            const invoiceNumber = document.getElementById("invoice_number");
            if (!invoiceNumber) {
              console.warn('Fusha e numrit të faturës me ID "invoice_number" nuk u gjet.');
            }

            const invoiceAmount = document.getElementById("invoice_amount");
            if (!invoiceAmount) {
              console.warn('Fusha e shumës së faturës me ID "invoice_amount" nuk u gjet.');
            }

            const invoiceDescription = document.getElementById("invoice_description");
            if (!invoiceDescription) {
              console.warn('Fusha e përshkrimit të faturës me ID "invoice_description" nuk u gjet.');
            }

            const invoiceStatus = document.getElementById("invoice_status");
            if (!invoiceStatus) {
              console.warn('Fusha e statusit të faturës me ID "invoice_status" nuk u gjet.');
            }

            const invoiceDueDate = document.getElementById("invoice_due_date");
            if (!invoiceDueDate) {
              console.warn('Fusha e datës së skadimit të faturës me ID "invoice_due_date" nuk u gjet.');
            }

            const invoiceCurrency = document.getElementById("invoice_currency");
            if (!invoiceCurrency) {
              console.warn('Fusha e valutës së faturës me ID "invoice_currency" nuk u gjet.');
            }

            const invoiceTax = document.getElementById("invoice_tax");
            if (!invoiceTax) {
              console.warn('Fusha e taksave të faturës me ID "invoice_tax" nuk u gjet.');
            }

            const invoiceNotes = document.getElementById("invoice_notes");
            if (!invoiceNotes) {
              console.warn('Fusha e shënimeve të faturës me ID "invoice_notes" nuk u gjet.');
            }

            const invoiceAttachments = document.getElementById("invoice_attachments");
            if (!invoiceAttachments) {
              console.warn('Fusha e bashkëngjitjeve të faturës me ID "invoice_attachments" nuk u gjet.');
            }

            const invoiceReference = document.getElementById("invoice_reference");
            if (!invoiceReference) {
              console.warn('Fusha e referencës së faturës me ID "invoice_reference" nuk u gjet.');
            }

          } catch (modalError) {
            console.error("Gabim gjatë konfigurimit të modalit:", modalError);
          }
        });

      } catch (error) {
        console.error("Gabim gjatë inicializimit të modalit:", error);
      }
    };

    // Funksion Asinkron i Imagjinuar për të Fetch-uar Vlerën e YouTube Analytics
    const fetchYouTubeAnalytics = async (channelId) => {
      try {
        // Këtu mund të shtoni thirrje të vërteta asinkrone, si fetch ose axios
        // Për shembull:
        // const response = await fetch(`/api/youtube-analytics?channelId=${channelId}`);
        // const data = await response.json();
        // return data.value;

        // Për momentin, do të kthejmë një vlerë fikse me një premisë asinkrone
        return new Promise((resolve) => {
          setTimeout(() => {
            resolve("Vlera e YouTube Analytics"); // Zëvendësoni me vlerën reale
          }, 1000);
        });
      } catch (fetchError) {
        console.error("Gabim gjatë fetchimit të YouTube Analytics:", fetchError);
        return "Vlerë e papërcaktuar"; // Vlerë default në rast gabimi
      }
    };

    // Inicializimi i Modalit Asinkron
    await initializeModal();

  } catch (error) {
    console.error("Gabim gjatë inicializimit:", error);
  }
});
