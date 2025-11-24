/**
 * Beispiel für Gruppe 3 (Dashboard / PHP/JavaScript)
 * Zeigt wie man den BAT-Man Auth-Service verwendet
 */

class AuthService {
    constructor() {
        this.apiUrl = 'https://eures.europa.eu/index_en/api/auth/login.php';
        this.tokenKey = 'batman_auth_token';
    }

    /**
     * Login beim Auth-Service
     */
    async login(username, password) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });

            if (response.ok) {
                const data = await response.json();
                
                // Token im localStorage speichern
                localStorage.setItem(this.tokenKey, data.token);
                
                // Optional: Token dekodieren und Role prüfen
                // const decoded = this.decodeJwtToken(data.token);
                // if (decoded.Role !== 'Admin') {
                //     this.logout();
                //     throw new Error('Zugriff verweigert: Nur für Admins');
                // }
                
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Login Error:', error);
            return false;
        }
    }

    /**
     * Logout (Client-seitig)
     */
    logout() {
        localStorage.removeItem(this.tokenKey);
        // Zur Login-Seite umleiten
        window.location.href = '/login.html';
    }

    /**
     * Token abrufen
     */
    getToken() {
        return localStorage.getItem(this.tokenKey);
    }

    /**
     * Authentifizierte Anfrage
     */
    async makeAuthenticatedRequest(url, options = {}) {
        const token = this.getToken();
        
        if (!token) {
            throw new Error('Not logged in');
        }

        const headers = {
            ...options.headers,
            'Authorization': `Bearer ${token}`
        };

        return fetch(url, { ...options, headers });
    }

    /**
     * JWT Token dekodieren (ohne Signatur-Prüfung!)
     * Nur für Client-seitige Prüfungen (Role, exp)
     */
    decodeJwtToken(token) {
        try {
            const base64Url = token.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(
                atob(base64).split('').map(c => 
                    '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
                ).join('')
            );
            return JSON.parse(jsonPayload);
        } catch (error) {
            console.error('Token decode error:', error);
            return null;
        }
    }

    /**
     * Token-Ablauf prüfen
     */
    isTokenExpired() {
        const token = this.getToken();
        if (!token) return true;

        const decoded = this.decodeJwtToken(token);
        if (!decoded || !decoded.exp) return true;

        return Date.now() >= decoded.exp * 1000;
    }
}

// Verwendung:
// const auth = new AuthService();
// await auth.login('admin', 'admin123');
// const response = await auth.makeAuthenticatedRequest('/api/data.php');
