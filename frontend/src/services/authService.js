import axios from "axios";
import Swal from "sweetalert2";

const API_URL = "http://localhost:8000/api/";
class AuthService {
  login(data) {
    return axios
      .post(API_URL + "login", data)
      .then(response => {
        if (response.data.token) {
          localStorage.setItem("user", response.data.token);
          localStorage.setItem("login", data.username);
        }
        
        return response.data.token;
      })
      .catch(function (error) {
        Swal.fire({
          icon: "error",
          title: "Oops, Login ou mot de passe incorrect !",
          showConfirmButton: true,
        });
        return null;
      });
  }

  logout() {
    localStorage.removeItem("user");
    localStorage.removeItem("login");
  }

  register(data) {
    return axios.post(API_URL + "register", data);
  }

  getCurrentUser() {
    return localStorage.getItem('user');;
  }
}
/* eslint import/no-anonymous-default-export: [2, {"allowNew": true}] */
export default new AuthService();