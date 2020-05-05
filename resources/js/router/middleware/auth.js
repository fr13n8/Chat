export default function auth ({ next, store }){
    if(!store.getters.auth){
        return next({
           name: 'SignIn'
        })
    }
    return next()
   }