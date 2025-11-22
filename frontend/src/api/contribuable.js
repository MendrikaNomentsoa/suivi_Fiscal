import http from "./http";

export default {

  getAll() {
    return http.get("/contribuables");
  },

  get(id) {
    return http.get(`/contribuables/${id}`);
  },

  create(data) {
    return http.post("/contribuables", data);
  },

  update(id, data) {
    return http.put(`/contribuables/${id}`, data);
  },

  delete(id) {
    return http.delete(`/contribuables/${id}`);
  }
};
