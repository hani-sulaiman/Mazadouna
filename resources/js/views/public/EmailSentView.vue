<template>
    <div class="email-sent-container">
      <div class="card">
        <h2>تم إرسال رابط التحقق</h2>
        <p>
          تم إرسال رابط تحقق من حسابك إلى البريد الإلكتروني:
          <strong>{{ email }}</strong>.
        </p>
        <p>
          يرجى التحقق من بريدك الوارد وإكمال عملية التحقق. إذا لم يصلك الرابط، يمكنك طلب إعادة الإرسال.
        </p>
        <router-link to="/sign" class="btn">العودة لتسجيل الدخول</router-link>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      email: {
        type: String,
        default: ''
      }
    }
  }
  </script>
  
  <style scoped>
  .email-sent-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    background: #f5f5f5;
  }
  .card {
    background: #fff;
    padding: 2rem;
    max-width: 500px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }
  .card h2 {
    color: #1976d2;
    margin-bottom: 1rem;
  }
  .card p {
    margin-bottom: 1rem;
    color: #333;
  }
  .btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #1976d2;
    color: #fff;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.3s;
  }
  .btn:hover {
    background: #1565c0;
  }
  </style>
  