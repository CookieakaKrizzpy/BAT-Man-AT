using System;
using System.Net.Http;
using System.Text;
using System.Text.Json;
using System.Threading.Tasks;

namespace BatManAuthExample
{
    /// <summary>
    /// Beispiel für Gruppe 1 (GUI / C#)
    /// Zeigt wie man den BAT-Man Auth-Service verwendet
    /// </summary>
    public class AuthService
    {
        private readonly HttpClient _httpClient;
        private string _token;

        public AuthService()
        {
            _httpClient = new HttpClient();
            _httpClient.BaseAddress = new Uri("https://eures.europa.eu/index_en/");
        }

        /// <summary>
        /// Login beim Auth-Service
        /// </summary>
        public async Task<bool> LoginAsync(string username, string password)
        {
            var loginData = new
            {
                username = username,
                password = password
            };

            var json = JsonSerializer.Serialize(loginData);
            var content = new StringContent(json, Encoding.UTF8, "application/json");

            try
            {
                var response = await _httpClient.PostAsync("api/auth/login.php", content);

                if (response.IsSuccessStatusCode)
                {
                    var responseBody = await response.Content.ReadAsStringAsync();
                    var result = JsonSerializer.Deserialize<LoginResponse>(responseBody);
                    
                    // Token speichern
                    _token = result.Token;
                    
                    // Optional: Token dekodieren und Role prüfen
                    // var claims = DecodeJwtToken(_token);
                    // if (claims.Role != "Teilnehmer") { /* Zugriff verweigern */ }
                    
                    return true;
                }
                
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Login Error: {ex.Message}");
                return false;
            }
        }

        /// <summary>
        /// Logout (Client-seitig)
        /// </summary>
        public void Logout()
        {
            _token = null;
            // Zur Login-Seite umleiten
        }

        /// <summary>
        /// Anfrage mit Authorization Header
        /// </summary>
        public async Task<string> MakeAuthenticatedRequestAsync(string endpoint)
        {
            if (string.IsNullOrEmpty(_token))
                throw new InvalidOperationException("Not logged in");

            _httpClient.DefaultRequestHeaders.Authorization = 
                new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", _token);

            var response = await _httpClient.GetAsync(endpoint);
            return await response.Content.ReadAsStringAsync();
        }
    }

    public class LoginResponse
    {
        public string Token { get; set; }
        public string Expires { get; set; }
    }
}
