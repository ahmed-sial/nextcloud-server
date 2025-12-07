describe("TC-SI-02", () => {
  it("tests TC-SI-02", () => {
    cy.viewport(1089, 825);
    cy.visit("http://localhost:8080/login?clear=1");
    cy.get("#user").click();
    cy.get("#user").type("uchihas");
    cy.get("#password").click();
    cy.get("#password").type("wrongPass");
      cy.get("fieldset > button svg").click();

  });
});
