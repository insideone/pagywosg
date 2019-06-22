import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/roles';
    }

    getList() {
        return this.axios.get(this.path())
    }
}
