import { createIPX, createIPXMiddleware } from 'ipx'

// https://github.com/unjs/ipx
const ipx = createIPX(/* options */)

export default createIPXMiddleware(ipx)
