import http from "./http";

export default {

  getAll() {
    return http.get("/declarations");
  },

  get(id) {
    return http.get(`/declarations/${id}`);
  },

  create(data) {
    return http.post("/declarations", data);
  },

  update(id, data) {
    return http.put(`/declarations/${id}`, data);
  },

  delete(id) {
    return http.delete(`/declarations/${id}`);
  }
};
