export default {
  async getChannels(url) {
    var resp = await fetch(url);
    var data = await resp.json();
    return data;
  },
};
