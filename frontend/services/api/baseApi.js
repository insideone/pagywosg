import path from 'path';
import axios from 'axios'

export default class BaseApi {
    constructor (props) {
        this.url = "/api";
        this.axios = axios;
    }

    path(...subpath) {
        subpath = subpath.map(part => part.toString());
        return path.join(this.url, ...subpath);
    }
}
