import axios from 'axios';
import authHeader from './auth-header';

const API_URL = 'http://localhost:8000/api';

export default class ProjetService {
  getUsers() {
    return axios.get(API_URL + '/users', { headers: authHeader() });
  }
  getUser(id) {
    return axios.get(API_URL + `/users/${id}`, { headers: authHeader() });
  }
  editUser(id, data) {
    return axios.patch(API_URL + `/users/${id}`, data, { headers: authHeader() });
  }
  deleteUser(id) {
    return axios.delete(API_URL + `/users/${id}`, { headers: authHeader() });
  }
  getCours() {
    return axios.get(API_URL + '/cours', { headers: authHeader() });
  }
  addCours(data) {
    return axios.post(API_URL + '/cours/add', data , { headers: authHeader() });
  }
  getCour(id) {
    return axios.get(API_URL + `/cours/${id}`, { headers: authHeader() });
  }
  editCours(id, data) {
    return axios.patch(API_URL + `/cours/${id}`, data,  { headers: authHeader() });
  }
  deleteCours(id) {
    return axios.delete(API_URL + `/cours/${id}`, { headers: authHeader() });
  }
  getChapitres() {
    return axios.get(API_URL + '/chapitre', { headers: authHeader() });
  }
  addChapitre(data) {
    return axios.post(API_URL + '/chapitre/add', data , { headers: authHeader() });
  }
  getChapitre(id) {
    return axios.get(API_URL + `/chapitre/${id}`, { headers: authHeader() });
  }
  getChapitreCours(id) {
    return axios.get(API_URL + `/chapitre/cours/${id}`, { headers: authHeader() });
  }
  editChapitre(id, data) {
    return axios.patch(API_URL + `/chapitre/${id}`, data,  { headers: authHeader() });
  }
  deleteChapitre(id) {
    return axios.delete(API_URL + `/chapitre/${id}`, { headers: authHeader() });
  }

  getNotes() {
    return axios.get(API_URL + '/notes', { headers: authHeader() });
  }
  addNote(data) {
    return axios.post(API_URL + '/notes/add', data , { headers: authHeader() });
  }
  getNote(id) {
    return axios.get(API_URL + `/notes/${id}`, { headers: authHeader() });
  }
  getUsersCoursNote(id,nom) {
    return axios.get(API_URL + `/notes/${nom}/${id}`, { headers: authHeader() });
  }
}
