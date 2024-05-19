**Tabela:** `access_denial_logs`

**Baza e të Dhënave:** `bareshao_f`

**Përshkrimi:** Kjo tabelë regjistron tentativat e mohimit të hyrjes për përdoruesit që përpiqen të kyçen në platformën e Rrjetit Baresha por nuk janë anëtarë të regjistruar të stafit. Sistemi ndërmerr menjëherë veprime në zbatim të këtyre tentativave.

**Fushat:**

1. `id` (Tipi: Numër i plotë) - Identifikues unik për secilën hyrje në regjistrin.
2. `ip_address` (Tipi: Varësi) - Adresa IP e klientit që përpiqet të hynë në sistemin.
3. `email_attempted` (Tipi: Varësi) - Adresa email e përdorur në tentativën e hyrjes.
4. `user_agent` (Tipi: Varësi) - Stringu i agentit të përdoruesit që tregon shfletuesin web dhe sistemin operativ të përdorur për tentativën e hyrjes.
5. `timestamp` (Tipi: Data dhe Ora) - Data dhe ora e tentativës së mohimit të hyrjes.

**Të Dhënat e marrura nga tabela ekzisutese:**

| id | ip_address    | email_attempted         | user_agent                                                                                                 | timestamp          |
|----|---------------|-------------------------|------------------------------------------------------------------------------------------------------------|--------------------|
| 1  | 82.114.85.0   | enisgjini11@gmail.com  | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 Edg/119.0.0.0 | 2023-11-05 23:39:49 |
| 2  | 37.35.71.39   | egjini17@gmail.com     | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 Edg/119.0.0.0 | 2023-11-09 19:08:43 |
| 3  | 185.82.111.53 | afrimkolgeci@gmail.com | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36        | 2023-11-10 10:14:36 |
| 4  | 185.82.111.27 | bareshafinance@gmail.com | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36       | 2023-11-11 13:09:10 |
| 5  | 185.82.111.27 | bareshafinance@gmail.com | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36       | 2023-11-11 13:10:31 |
| 6  | 185.82.111.27 | bareshafinance@gmail.com | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36       | 2023-11-11 13:10:46 |
| 7  | 185.82.111.27 | bareshafinance@gmail.com | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36       | 2023-11-11 13:12:33 |
| 8  | 185.82.111.27 | besmirakolgeci1@gmail.com | Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36       | 2023-11-11 13:38:22 |

Ky dokumentim ofron një pasqyrë të qartë të qëllimit, strukturës dhe të dhënave shembull të tabelës `access_denial_logs`, duke e bërë më të lehtë për përdoruesit të kuptojnë dhe të përdorin tabelën në mënyrë efektive.