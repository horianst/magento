## Manual instalare modul Cargus Magento

### Abonarea la API

- Se acceseaza https://urgentcargus.portal.azure-api.net/
- Se apasa butonul `Sign up` si se completeaza formularul (nu se pot folosi credentialele pe care clientul le are pentru UrgentOnline / WebExpress)
- Se confirma inregistrarea prin click pe link-ul primit pe mail (trebuie folosita o adresa de email reala)
- In pagina https://urgentcargus.portal.azure-api.net/developer se da click pe `PRODUCTS` in menu, apoi pe `UrgentOnlineAPI` si se apasa `Subscribe`, apoi `Confirm`
- Dupa ce echipa Cargus confirma subscriptia la API, clientul primeste un email de confirmare
- In pagina https://urgentcargus.portal.azure-api.net/developer se da click pe numele utilizatorului din partea dreapta-sus, apoi se apasa `Profile`
- Cele doua subscription keys sunt mascate de caracterele `xxx...xxx` si se apasa `Show` in dreptul fiecareia pentru afisare
- Se recomanda utilizarea `Primary key` in modulul Cargus

### Instalarea modulului

- Copiaza modulul in /app/code
- in linia de comanda intra in directorul radacina al proiectului
- ruleaza comanda `bin/magento module:status`, Modulul Urgent_Cargus ar trebui sa fie disabled
- ca sa activezi modulul ruleaza comanda `bin/magento module:enable Urgent_Cargus`
- pentru ca modificarile sa aiba efect ruleaza comenzile: `bin/magento setup:upgrade`, `bin/magento setup:di:compile` si `bin/magento cache:flush`
- Se acceseaza pagina `Stores`, `Configuration`, `Sales`, `Shipping Methods`, se deschide tab-ul `Cargus`, se completeaza formularul si se apasa butonul portocaliu `Save Config` din partea dreapta-sus a paginii

### Configurarea modulului

- Enabled: se alege daca metoda de livrare este activa sau nu
- Ship to Aplicable Countries: se alege daca metoda de livrare este activa pentru toate tarile sau doar pentru unele
- Ship to Specific Countries: daca metoda de livrare este activa doar pentru anumite tari,  se aleg de aici
- Sort Order: se adauga o valoare numerica, aferenta ordinii intre celelalte metode de livrare active
- API Url: https://urgentcargus.azure-api.net/api
- Subscription Key: Primary key obtinuta in pasul A. Abonarea la API
- Username: numele de utilizator al contului clientului in platforma UrgentOnline / WebExpress
- Password: parola aferenta contului mentionat mai sus

### Setarea preferintelor in modul

- Se acceseaza tabul din meniu  `Cargus`, apoi `Preferinte` si se completeaza formularul, dupa care se apasa butonul portocaliu `Salveaza preferintele` din partea de jos a paginii
- Punctul de ridicare: se alege unul din punctele de ridicare dispnibile. Daca nu exista niciun punct de ridicare disponibil, trebuie adaugat unul din UrgentOnline / WebExpress
- Deschidere colet: se alege daca este utilizat serviciul deschidere colet
- Asigurare: se alege daca livrarea se face cu asigurare sau fara
- Livrare sambata: se alege daca este permisa livrarea in zilele de sambata
- Livrare dimineata: se alge daca este utilizat serviciul livrare matinala
- Ramburs: se alege tipul rambursului – Numerar sau Cont colector
- Platitor: se alege platitorul costului de livrare – Expeditorul sau Destinatarul
- Localitati: se alege daca la dropdown-urile pentru localitati se utilizeaza lista salvata local sau se apeleaza live serviciul
- Tip expeditie implicita: se alege tipul de expeditie uzuala – Colet sau Plic
- Cost fix transport: se alege un cost fix de livrare sau se lasa necompletat, pentru ca modulul sa calculeze automat tariful