import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor() {
        super();
        this.url = '/api/profile';
    }

    logged() {
        return this.axios.get(this.path())
    }
}
